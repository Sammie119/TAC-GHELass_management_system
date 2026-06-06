<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\IncomeRecord;
use App\Models\Member;
use App\Models\MemberSession;
use App\Models\OnlinePayment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MemberPortalController extends Controller
{
    // ── Login page ─────────────────────────────────────────
    public function login()
    {
        return view('portal.login');
    }

    // ── Lookup member by ID, phone or email ────────────────
    public function lookup(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string|min:2',
        ]);

        $identifier = trim($request->identifier);

        $member = Member::where('status', 'active')
            ->where(function ($q) use ($identifier) {
                $q->where('member_id_card', $identifier)
                    ->orWhere('phone',        $identifier)
                    ->orWhere('tacms_number',        $identifier);
            })->first();

        if (!$member) {
            return back()->withErrors([
                'identifier' => 'No active member found with that ID, phone, or email.',
            ])->withInput();
        }

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in session (expires in 10 minutes)
        session([
            'portal_member_id' => $member->id,
            'portal_otp'       => $otp,
            'portal_otp_exp'   => now()->addMinutes(10)->timestamp,
        ]);

        // In production this would be sent via SMS/email
        // For now we show it on screen (development mode)
        return redirect()->route('portal.otp.show');
//        return view('portal.otp', compact('member', 'otp'));
    }

    // ── Verify OTP ─────────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $memberId = session('portal_member_id');
        $storedOtp = session('portal_otp');
        $otpExp = session('portal_otp_exp');
        $member = Member::find($memberId);

        if (!is_null($member->otp)){
            $storedOtp = $member->otp;
        }

        if (!$memberId || !$storedOtp) {
            return redirect()->route('portal.login')
                ->withErrors(['otp' => 'Session expired. Please log in again.']);
        }

        if (is_null($member->otp)){
            if (now()->timestamp > $otpExp) {
                return redirect()->route('portal.login')
                    ->withErrors(['otp' => 'OTP expired. Please log in again.']);
            }
        }

        if ($request->otp != $storedOtp) {
            return back()->withErrors(['otp' => 'Incorrect OTP. Please try again.']);
        }

        if (is_null($member->otp)){
            $member->update(['otp' => $storedOtp]);

            // Create portal session token
            $token = Str::random(64);

            MemberSession::updateOrCreate(
                [
                    'member_id' => $memberId
                ],
                [
                    'otp'            => $storedOtp,
                    'token'          => $token,
                    'last_active_at' => now(),
                    'ip_address'     => $request->ip(),
                ]
            );
        } else {
            $member_login = MemberSession::where('member_id', $memberId)->first();
            $token = $member_login->token;
        }

        // Clear OTP from session
        session()->forget(['portal_member_id', 'portal_otp', 'portal_otp_exp']);

        // Store token in session
        session(['portal_token' => $token]);

        return redirect()->route('portal.dashboard');
    }

    public function showOtp()
    {
        // If no OTP session exists redirect back to login
        if (!session('portal_member_id') || !session('portal_otp')) {
            return redirect()->route('portal.login')
                ->withErrors(['identifier' => 'Session expired. Please try again.']);
        }

        $otp        = session('portal_otp');
        $memberName = session('portal_member_name');

        // Create a temporary member object for the view
        $member = Member::find(session('portal_member_id'));

        return view('portal.otp', compact('member', 'otp'));
    }

    // ── Portal dashboard ───────────────────────────────────
    public function dashboard(Request $request)
    {
        if(isset($request['reference'])){
            $response = PaymentService::verifyTransaction($request['reference']);

//            dd($response);
            if ($response['status'] && $response['data']['status'] === 'success') {

                $member = Member::where('email', $response['data']['customer']['email'])->first();

                if($member){
                    $payment = OnlinePayment::where('member_id', $member->id)->orderByDesc('id')->first();
                    $payment->update([
                        'reference' => $response['data']['reference'],
                        'provider'  => $response['data']['channel'],
                        'status'    => 'confirmed',
                        'confirmed_at' => now(),
                        'confirmed_by' => 1,
                    ]);

                    // Create income record
                    IncomeRecord::create([
                        'member_id'      => $member->id,
                        'category'       => $payment->category,
                        'amount'         => $payment->amount,
                        'currency'       => $payment->currency,
                        'amount_ghs'     => $payment->amount,
                        'exchange_rate'  => 1,
                        'payment_date'   => today(),
                        'payment_method' => 'online',
                        'reference'      => $response['data']['reference'],
                        'notes'          => 'Online payment confirmed',
                        'status'         => 'confirmed',
                        'recorded_by'    => 1,
                    ]);
                }
            }
        }

        $member = $this->getAuthMember();

        if($member){
            $member_session = MemberSession::where('member_id', $member->id)->first();
        }

        if (!$member) return redirect()->route('portal.login');

        $recentAttendance = $member->attendance()
            ->with('event')
            ->latest('checked_in_at')
            ->take(5)->get();

        $totalAttendance = $member->attendance()->count();

        $upcomingEvents = Event::whereIn('status', ['upcoming', 'active'])
            ->where('event_date', '>=', today())
            ->orderBy('event_date')->take(3)->get();

        $thisMonthCount = $member->attendance()
            ->whereMonth('checked_in_at', now()->month)
            ->whereYear('checked_in_at',  now()->year)
            ->count();

        $streak = $this->calculateStreak($member);

        // ── Payment history ────────────────────────────────────
        $recentPayments = \App\Models\IncomeRecord::where('member_id', $member->id)
            ->where('status', 'confirmed')
            ->latest('payment_date')
            ->take(5)->get();

        $totalPaid = \App\Models\IncomeRecord::where('member_id', $member->id)
            ->where('status', 'confirmed')
            ->sum('amount_ghs');

        $totalTithe = \App\Models\IncomeRecord::where('member_id', $member->id)
            ->where('status', 'confirmed')
            ->where('category', 'tithe')
            ->sum('amount_ghs');

        $thisMonthPaid = \App\Models\IncomeRecord::where('member_id', $member->id)
            ->where('status', 'confirmed')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date',  now()->year)
            ->sum('amount_ghs');

        // ── Pending online payments ────────────────────────────
        $pendingPayments = \App\Models\OnlinePayment::where('member_id', $member->id)
            ->where('status', 'pending')
            ->latest()->take(3)->get();

        return view('portal.dashboard', compact(
            'member', 'recentAttendance', 'totalAttendance',
            'upcomingEvents', 'thisMonthCount', 'streak',
            'recentPayments', 'totalPaid', 'totalTithe',
            'thisMonthPaid', 'pendingPayments', 'member_session'
        ));
    }

    // ── Full attendance history ────────────────────────────
    public function attendance(Request $request)
    {
        $member = $this->getAuthMember();
        if (!$member) return redirect()->route('portal.login');

        $attendance = $member->attendance()
            ->with('event')
            ->latest('checked_in_at')
            ->paginate(15);

        return view('portal.attendance', compact('member', 'attendance'));
    }

    // ── Profile page ───────────────────────────────────────
    public function profile()
    {
        $member = $this->getAuthMember();
        if (!$member) return redirect()->route('portal.login');

        return view('portal.profile', compact('member'));
    }

    // ── Update contact details ─────────────────────────────
    public function updateProfile(Request $request)
    {
        $member = $this->getAuthMember();
        if (!$member) return redirect()->route('portal.login');

        $validated = $request->validate([
            'phone'   => 'nullable|string|max:20|unique:members,phone,' . $member->id,
            'email'   => 'nullable|email|max:150|unique:members,email,' . $member->id,
            'address' => 'nullable|string|max:255',
            'otp'     => 'required|numeric|digits:6',
        ]);

        $member->update($validated);

        return back()->with('success', 'Your details have been updated.');
    }

    // ── Download QR code ───────────────────────────────────
    public function downloadQr()
    {
        $member = $this->getAuthMember();
        if (!$member) return redirect()->route('portal.login');

        $qrPath = 'qrcodes/' . $member->qr_code . '.svg';

        if (!Storage::disk('public')->exists($qrPath)) {
            $qrContent = QrCode::format('svg')->size(300)->generate($member->qr_code);
            Storage::disk('public')->put($qrPath, $qrContent);
        }

        return response(Storage::disk('public')->get($qrPath))
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $member->member_id_card . '-qr.svg"');
    }

    // ── Logout ─────────────────────────────────────────────
    public function logout()
    {
        session()->forget('portal_token');

        return redirect()->route('portal.login')
            ->with('success', 'You have been logged out.');
    }

    // ── Helpers ────────────────────────────────────────────
    private function getAuthMember(): ?Member
    {
        $token = session('portal_token');
        if (!$token) return null;

        $session = MemberSession::where('token', $token)->first();
        if (!$session) return null;

        $session->update(['last_active_at' => now()]);

        return $session->member;
    }

    private function calculateStreak(Member $member): int
    {
        $events = Event::whereIn('status', ['closed', 'active'])
            ->orderBy('event_date', 'desc')
            ->take(20)->get();

        $streak = 0;
        foreach ($events as $event) {
            $attended = Attendance::where('member_id', $member->id)
                ->where('event_id', $event->id)->exists();
            if ($attended) {
                $streak++;
            } else {
                break;
            }
        }
        return $streak;
    }

    public function paymentPage()
    {
        $member = $this->getAuthMember();
        if (!$member) return redirect()->route('portal.login');

        return view('portal.pay', compact('member'));
    }

    public function submitPayment(Request $request)
    {
        $member = $this->getAuthMember();
        if (!$member) return redirect()->route('portal.login');

        $validated = $request->validate([
            'category' => 'required|string',
            'amount'   => 'required|numeric|min:1',
            'phone'    => 'required|string',
            'notes'    => 'nullable|string|max:255',
        ]);

        $reference = 'ONL-' . strtoupper(substr(md5(uniqid()), 0, 8));

        OnlinePayment::create([
            'member_id' => $member->id,
            'category'  => $validated['category'],
            'amount'    => $validated['amount'],
            'currency'  => 'GHS',
            'phone'     => $validated['phone'],
            'reference' => $reference,
            'provider'  => 'momo',
            'status'    => 'pending',
            'notes'     => $validated['notes'],
        ]);

        if ($validated['category'] === 'project')
            PaymentService::makePayment($member->email, $validated['amount'], config('services.paystack.call_back_url'), 'Project');
        else
            PaymentService::makePayment($member->email, $validated['amount'], config('services.paystack.call_back_url'),'Main');

//        return back()->with('success', "Payment request submitted! Reference: {$reference}. Our finance team will confirm shortly.");
    }

    public function payments()
    {
        $member = $this->getAuthMember();
        if (!$member) return redirect()->route('portal.login');

        $payments = \App\Models\IncomeRecord::where('member_id', $member->id)
            ->where('status', 'confirmed')
            ->latest('payment_date')
            ->paginate(15);

        $summary = [
            'total'     => \App\Models\IncomeRecord::where('member_id', $member->id)->where('status', 'confirmed')->sum('amount_ghs'),
            'tithe'     => \App\Models\IncomeRecord::where('member_id', $member->id)->where('status', 'confirmed')->where('category', 'tithe')->sum('amount_ghs'),
            'offering'  => \App\Models\IncomeRecord::where('member_id', $member->id)->where('status', 'confirmed')->where('category', 'offering')->sum('amount_ghs'),
            'this_year' => \App\Models\IncomeRecord::where('member_id', $member->id)->where('status', 'confirmed')->whereYear('payment_date', now()->year)->sum('amount_ghs'),
        ];

        return view('portal.payments', compact('member', 'payments', 'summary'));
    }
}
