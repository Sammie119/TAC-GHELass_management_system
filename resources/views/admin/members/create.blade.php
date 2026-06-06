@extends('layouts.admin')
@section('page-title', 'Add Member')
@section('content')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Add New Member</h2>
            <p class="text-sm text-gray-400 mt-0.5">Fill in the details below to register a new member</p>
        </div>
        <a href="{{ route('admin.members.index') }}"
           class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
            ← Back to members
        </a>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
            <p class="font-medium mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.members.store') }}"
          enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left: Photo upload --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    <div id="photo-preview"
                         class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center
                            text-blue-600 font-bold text-2xl mx-auto mb-4">
                        ?
                    </div>
                    <p class="text-sm font-medium text-gray-700 mb-2">Profile photo</p>
                    <label class="cursor-pointer">
                    <span class="text-xs bg-gray-100 border border-gray-300 text-gray-600
                                 px-3 py-1.5 rounded-lg hover:bg-gray-200 transition-colors">
                        Choose photo
                    </span>
                        <input type="file" name="photo" accept="image/*" class="hidden"
                               onchange="previewPhoto(this)">
                    </label>
                    <p class="text-xs text-gray-400 mt-2">JPG, PNG up to 2MB</p>
                </div>
            </div>

            {{-- Right: Form fields --}}
            <div class="lg:col-span-2 space-y-5">

                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Personal information</h3>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                First name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name" value="{{ old('first_name', $prefill['first_name'] ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-blue-500 focus:outline-none
                                      @error('first_name') border-red-400 @enderror"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Last name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="last_name" value="{{ old('last_name',  $prefill['last_name']  ?? '') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-blue-500 focus:outline-none
                                      @error('last_name') border-red-400 @enderror"
                                   required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone',      $prefill['phone']      ?? '') }}"
                                   placeholder="e.g. 0244000001"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-blue-500 focus:outline-none
                                      @error('phone') border-red-400 @enderror">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="eg. example@example.com"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-blue-500 focus:outline-none
                                      @error('email') border-red-400 @enderror">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <select name="gender"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Select gender</option>
                                <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               placeholder="e.g. 12 Church Street, Accra"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                  focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    {{-- Department & TACMS --}}
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department / Ministry</label>
                            <select name="department"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                       focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Select department</option>
                                @foreach(config('departments') as $dept)
                                    <option value="{{ $dept }}" {{ old('department') === $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">TACMS Number</label>
                            <input type="text" name="tacms_number" value="{{ old('tacms_number') }}"
                                   placeholder="e.g. TAC00ABC010101"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                      focus:ring-2 focus:ring-blue-500 focus:outline-none
                                      @error('tacms_number') border-red-400 @enderror">
                        </div>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.members.index') }}"
                       class="px-5 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">
                        Save member
                    </button>
                </div>

            </div>
        </div>
    </form>

    <script>
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    const preview = document.getElementById('photo-preview');
                    preview.innerHTML = `<img src="${e.target.result}"
                class="w-24 h-24 rounded-full object-cover mx-auto">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

@endsection
