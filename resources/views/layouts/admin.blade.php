<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicons --}}
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="shortcut icon" href="/favicon.svg">
    <link rel="apple-touch-icon" href="/favicon.svg">

    <title>{{ config('app.name') }} — Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR ===== --}}
    <aside id="sidebar"
           class="w-64 bg-white border-r border-gray-200 flex flex-col fixed inset-y-0 left-0 z-50
                  transform -translate-x-full lg:translate-x-0 transition-transform duration-200">

        {{-- Logo --}}
        <div class="h-16 flex items-center px-6 border-b border-gray-100">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <span class="font-semibold text-gray-800 text-sm">{{ config('app.name', 'Church Management System') }}</span>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            {{-- Dashboard (admin only) --}}
            @hasanyrole('admin|membership|finance|usher')
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.dashboard')
                     ? 'bg-blue-50 text-blue-700 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
            @endhasanyrole

            {{-- Members (admin only) --}}
            @hasanyrole('admin|membership')
            <div class="pt-3 pb-1">
                <p class="px-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Membership</p>
            </div>

            <a href="{{ route('admin.members.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
              {{ request()->routeIs('admin.members.*')
                 ? 'bg-blue-50 text-blue-700 font-medium'
                 : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Members
            </a>

            <a href="{{ route('admin.members.archived') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
              {{ request()->routeIs('admin.members.archived')
                 ? 'bg-red-50 text-red-600 font-medium'
                 : 'text-gray-400 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8M10 12v4m4-4v4"/>
                </svg>
                Archived
            </a>

            <a href="{{ route('admin.visitors.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
              {{ request()->routeIs('admin.visitors.*')
                 ? 'bg-blue-50 text-blue-700 font-medium'
                 : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Visitors
            </a>

            <a href="{{ route('admin.absentees.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.absentees.*')
                     ? 'bg-red-50 text-red-600 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Follow-up
            </a>

            <a href="{{ route('admin.souls.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.souls.*')
                     ? 'bg-blue-50 text-blue-700 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                New Souls
            </a>

            <a href="{{ route('admin.cells.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.cells.*')
                     ? 'bg-blue-50 text-blue-700 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zM7 10a2 2 0 11-4 0 2 2 0 014 0zM17 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Cell Groups
            </a>
            @endhasanyrole

            {{-- Events (admin only) --}}
            @role('admin')
            <div class="pt-3 pb-1">
                <p class="px-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Attendance</p>
            </div>

            <a href="{{ route('admin.events.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
              {{ request()->routeIs('admin.events.*')
                 ? 'bg-blue-50 text-blue-700 font-medium'
                 : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Events
            </a>
            @endrole

            {{-- Check-in (admin + usher) --}}
            @hasanyrole('admin|usher|membership')
            <a href="{{ route('admin.checkin.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
              {{ request()->routeIs('admin.checkin.*')
                 ? 'bg-blue-50 text-blue-700 font-medium'
                 : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                Check-in
            </a>
            @endhasanyrole

            @role('usher')
                <a href="{{ route('admin.visitors.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.visitors.*')
                     ? 'bg-blue-50 text-blue-700 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Visitors
                </a>
            @endrole

            {{-- Finance (admin + finance role) --}}
            @hasanyrole('admin|finance')
            <div class="pt-3 pb-1">
                <p class="px-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Finance</p>
            </div>

            <a href="{{ route('admin.finance.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.finance.index')
                     ? 'bg-green-50 text-green-700 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Finance Dashboard
            </a>

            <a href="{{ route('admin.finance.income') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.finance.income')
                     ? 'bg-green-50 text-green-700 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                </svg>
                Income
            </a>

            <a href="{{ route('admin.finance.sunday-tithes') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.finance.sunday-tithes')
                     ? 'bg-green-50 text-green-700 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Sunday Tithes
            </a>

            <a href="{{ route('admin.finance.expenses') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.finance.expenses')
                     ? 'bg-green-50 text-green-700 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                </svg>
                Expenses
            </a>

            <a href="{{ route('admin.finance.member-tithes') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.finance.member-tithes')
                     ? 'bg-green-50 text-green-700 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Member Tithes
            </a>

            <a href="{{ route('admin.finance.online-payments') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.finance.online-payments')
                     ? 'bg-green-50 text-green-700 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Online Payments
            </a>

            <a href="{{ route('admin.pledges.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.pledges.*')
                     ? 'bg-green-50 text-green-700 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Pledges
            </a>

            <a href="{{ route('admin.finance.report') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.finance.report')
                     ? 'bg-green-50 text-green-700 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Finance Report
            </a>
            @endhasanyrole

            {{-- Reports section (admin only) --}}
            @hasanyrole('admin|membership')
            <div class="pt-3 pb-1">
                <p class="px-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Reports</p>
            </div>

            <a href="{{ route('admin.reports.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
              {{ request()->routeIs('admin.reports.index')
                 ? 'bg-blue-50 text-blue-700 font-medium'
                 : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Reports
            </a>

            <a href="{{ route('admin.reports.absentees') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
              {{ request()->routeIs('admin.reports.absentees')
                 ? 'bg-blue-50 text-blue-700 font-medium'
                 : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                Absentees Report
            </a>

            <a href="{{ route('admin.reports.souls') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                  {{ request()->routeIs('admin.reports.souls')
                     ? 'bg-blue-50 text-blue-700 font-medium'
                     : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                New Souls Report
            </a>
            @endhasanyrole

            {{-- Settings section (admin only) --}}
            @role('admin')
            <div class="pt-3 pb-1">
                <p class="px-3 text-xs font-medium text-gray-400 uppercase tracking-wider">Settings</p>
            </div>

            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
              {{ request()->routeIs('admin.users.*')
                 ? 'bg-blue-50 text-blue-700 font-medium'
                 : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Users & Roles
            </a>

            <a href="{{ route('admin.notifications.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                      {{ request()->routeIs('admin.notifications.*')
                         ? 'bg-blue-50 text-blue-700 font-medium'
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                Notifications
            </a>

            @if(auth()->id() === 1)
                <a href="{{ route('admin.settings.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                      {{ request()->routeIs('admin.settings.*')
                         ? 'bg-blue-50 text-blue-700 font-medium'
                         : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </a>
            @endif
            @endrole

        </nav>

        {{-- Bottom user info --}}
        <div style="border-top:1px solid #f3f4f6;padding:12px;">
            <div style="display:flex;align-items:center;gap:10px;">

                {{-- Avatar — clickable, links to profile --}}
                <a href="{{ route('admin.profile.show') }}"
                   style="text-decoration:none;flex-shrink:0;">
                    @if(auth()->user()->profile_photo)
                        <img src="{{ Storage::url(auth()->user()->profile_photo) }}"
                             style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid #e5e7eb;">
                    @else
                        <div style="width:36px;height:36px;border-radius:50%;background:#2563eb;display:flex;align-items:center;justify-content:center;color:white;font-size:13px;font-weight:700;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                    @endif
                </a>

                <div style="flex:1;min-width:0;">
                    <a href="{{ route('admin.profile.show') }}"
                       style="text-decoration:none;">
                        <p style="font-size:13px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ auth()->user()->name }}
                        </p>
                        <p style="font-size:11px;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ auth()->user()->email }}
                        </p>
                    </a>
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}" style="flex-shrink:0;">
                    @csrf
                    <button type="submit" title="Logout"
                            style="color:#9ca3af;background:none;border:none;cursor:pointer;padding:4px;"
                            onmouseenter="this.style.color='#ef4444'"
                            onmouseleave="this.style.color='#9ca3af'">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>

            </div>
        </div>

    </aside>

    {{-- ===== MAIN AREA ===== --}}
    <div class="flex-1 flex flex-col lg:ml-64 min-h-screen">

        {{-- Top navbar --}}
        <header class="h-16 bg-white border-b border-gray-200 flex items-center px-4 lg:px-6 gap-4 sticky top-0 z-40">

            {{-- Mobile menu toggle --}}
            <button onclick="toggleSidebar()"
                    class="lg:hidden text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page title --}}
            <h1 class="text-base font-semibold text-gray-800 flex-1">
                @yield('page-title', 'Dashboard')
            </h1>

            {{-- Right side actions --}}
            <div class="flex items-center gap-3">
                {{-- Member portal link --}}
                <a href="{{ route('portal.login') }}" target="_blank"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors
                text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Member Portal
                </a>

                {{-- Active event badge --}}
                @php
                    $activeEvent = \App\Models\Event::where('status','active')->latest()->first();
                @endphp
                @if($activeEvent)
                    <span class="hidden sm:flex items-center gap-1.5 bg-green-50 text-green-700
                                 border border-green-200 px-3 py-1 rounded-full text-xs font-medium">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        {{ $activeEvent->title }}
                    </span>
                @endif

                {{-- Quick check-in button --}}
                <a href="{{ route('admin.checkin.index') }}"
                   class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-700 transition-colors">
                    Check-in
                </a>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mx-4 lg:mx-6 mt-4 bg-green-50 border border-green-200 text-green-800
                        px-4 py-3 rounded-lg text-sm flex justify-between items-center">
                {{ session('success') }}
                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">✕</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-4 lg:mx-6 mt-4 bg-red-50 border border-red-200 text-red-800
                        px-4 py-3 rounded-lg text-sm flex justify-between items-center">
                {{ session('error') }}
                <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">✕</button>
            </div>
        @endif

        {{-- Page content --}}
        <main class="flex-1 p-4 lg:p-6 overflow-y-auto">
            @yield('content')
        </main>

    </div>
</div>

{{-- Mobile sidebar overlay --}}
<div id="sidebar-overlay"
     onclick="toggleSidebar()"
     class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden">
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>

</body>
</html>
