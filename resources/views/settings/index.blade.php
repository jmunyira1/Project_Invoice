<x-app-layout>
    <x-slot name="title">Settings</x-slot>
    <x-slot name="breadcrumb">
        <li class="breadcrumb-item active">Settings</li>
    </x-slot>

    {{-- Tab nav --}}
    <ul class="nav nav-tabs border-tab mb-4" id="settingsTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ !request('tab') || request('tab') === 'organisation' ? 'active' : '' }}"
               href="?tab=organisation">
                <i data-feather="briefcase" style="width:14px;height:14px" class="me-1"></i>
                Organisation
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('tab') === 'team' ? 'active' : '' }}"
               href="?tab=team">
                <i data-feather="users" style="width:14px;height:14px" class="me-1"></i>
                Team
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('tab') === 'account' ? 'active' : '' }}"
               href="?tab=account">
                <i data-feather="user" style="width:14px;height:14px" class="me-1"></i>
                My Account
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('tab') === 'password' ? 'active' : '' }}"
               href="?tab=password">
                <i data-feather="lock" style="width:14px;height:14px" class="me-1"></i>
                Password
            </a>
        </li>
    </ul>

    {{-- ── TAB: Organisation ─────────────────────────────────── --}}
    @if(!request('tab') || request('tab') === 'organisation')

        <div class="row">
            {{-- Logo card --}}
            <div class="col-xl-4 col-lg-5">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="mb-4">Organisation Logo</h6>

                        {{-- Logo display --}}
                        <div class="mb-3">
                            @if($org->logo_path)
                                <img src="{{ Storage::url($org->logo_path) }}"
                                     alt="{{ $org->name }}"
                                     class="img-fluid rounded mb-2"
                                     style="max-height:100px;max-width:200px;object-fit:contain;">
                            @else
                                <div
                                    class="rounded bg-light d-flex align-items-center justify-content-center mx-auto mb-2"
                                    style="width:100px;height:100px;">
                                    <i data-feather="image" style="width:36px;height:36px" class="text-muted"></i>
                                </div>
                            @endif
                            <p class="text-muted f-13">
                                {{ $org->logo_path ? 'Current logo' : 'No logo uploaded' }}
                            </p>
                        </div>

                        {{-- Upload form --}}
                        <form method="POST"
                              action="{{ route('settings.logo.upload') }}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <input type="file"
                                       name="logo"
                                       id="logo"
                                       accept="image/png,image/jpeg,image/svg+xml"
                                       class="form-control form-control-sm @error('logo') is-invalid @enderror">
                                @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">PNG, JPG or SVG · max 2MB</small>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                Upload Logo
                            </button>
                        </form>

                        @if($org->logo_path)
                            <form method="POST"
                                  action="{{ route('settings.logo.remove') }}"
                                  class="mt-2"
                                  onsubmit="return confirm('Remove the logo?')">
                                @csrf
                                <button type="submit" class="btn btn-light btn-sm w-100 text-danger">
                                    Remove Logo
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Organisation profile form --}}
            <div class="col-xl-8 col-lg-7">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Organisation Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('settings.organisation.update') }}">
                            @csrf @method('PATCH')
                            <div class="row g-3">

                                <div class="col-md-8">
                                    <label class="form-label">Organisation Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                           name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $org->name) }}"
                                           required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Currency <span class="text-danger">*</span></label>
                                    <select name="currency"
                                            class="form-select @error('currency') is-invalid @enderror">
                                        @foreach(['KES' => 'KES — Kenyan Shilling', 'USD' => 'USD — US Dollar', 'GBP' => 'GBP — British Pound', 'EUR' => 'EUR — Euro', 'ZAR' => 'ZAR — South African Rand', 'UGX' => 'UGX — Ugandan Shilling', 'TZS' => 'TZS — Tanzanian Shilling'] as $code => $label)
                                            <option
                                                value="{{ $code }}" {{ old('currency', $org->currency) === $code ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('currency')
                                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email"
                                           name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', $org->email) }}"
                                           required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text"
                                           name="phone"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $org->phone) }}"
                                           placeholder="+254 7XX XXX XXX">
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <textarea name="address"
                                              rows="3"
                                              class="form-control @error('address') is-invalid @enderror"
                                              placeholder="Street, City, Country">{{ old('address', $org->address) }}</textarea>
                                    @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        Save Organisation Details
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endif

    {{-- ── TAB: Team ─────────────────────────────────────────── --}}
    @if(request('tab') === 'team')

        <div class="row">
            <div class="col-xl-8">

                {{-- Current members --}}
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h5>Team Members</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Member</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    @if(auth()->user()->isOwner())
                                        <th class="text-end">Actions</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div
                                                    class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                                    style="width:34px;height:34px;font-size:12px;font-weight:600;">
                                                    {{ $member->initials }}
                                                </div>
                                                <div>
                                                    <span class="fw-semibold d-block">{{ $member->name }}</span>
                                                    <small class="text-muted">{{ $member->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if(auth()->user()->isOwner() && $member->id !== auth()->id())
                                                <form method="POST"
                                                      action="{{ route('settings.team.role', $member) }}">
                                                    @csrf @method('PATCH')
                                                    <select name="role"
                                                            class="form-select form-select-sm"
                                                            style="width:auto"
                                                            onchange="this.form.submit()">
                                                        <option
                                                            value="member" {{ $member->role === 'member' ? 'selected' : '' }}>
                                                            Member
                                                        </option>
                                                        <option
                                                            value="owner" {{ $member->role === 'owner'  ? 'selected' : '' }}>
                                                            Owner
                                                        </option>
                                                    </select>
                                                </form>
                                            @else
                                                <span
                                                    class="badge badge-light-{{ $member->role === 'owner' ? 'primary' : 'secondary' }} text-capitalize">
                                                {{ $member->role }}
                                            </span>
                                                @if($member->id === auth()->id())
                                                    <small class="text-muted ms-1">(you)</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-muted f-13">
                                            {{ $member->created_at->format('d M Y') }}
                                        </td>
                                        @if(auth()->user()->isOwner())
                                            <td class="text-end">
                                                @if($member->id !== auth()->id())
                                                    <form method="POST"
                                                          action="{{ route('settings.team.remove', $member) }}"
                                                          onsubmit="return confirm('Remove {{ addslashes($member->name) }} from the team?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-light btn-sm text-danger">
                                                            <i data-feather="user-x" style="width:13px;height:13px"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Add member --}}
            @if(auth()->user()->isOwner())
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h5>Add Team Member</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('settings.team.invite') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}"
                                           placeholder="Full name"
                                           required>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email"
                                           name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}"
                                           placeholder="email@example.com"
                                           required>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Role <span class="text-danger">*</span></label>
                                    <select name="role"
                                            class="form-select @error('role') is-invalid @enderror">
                                        <option value="member" selected>Member</option>
                                        <option value="owner">Owner</option>
                                    </select>
                                    @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Temporary Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password"
                                           name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           placeholder="Min 8 characters"
                                           required>
                                    <small class="text-muted">They can change this after logging in.</small>
                                    @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    Add Member
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    @endif

    {{-- ── TAB: My Account ───────────────────────────────────── --}}
    @if(request('tab') === 'account')

        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="card">
                    <div class="card-header pb-0"><h5>My Profile</h5></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('settings.profile.update') }}">
                            @csrf @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}"
                                       required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email"
                                       name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}"
                                       required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                Update Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endif

    {{-- ── TAB: Password ─────────────────────────────────────── --}}
    @if(request('tab') === 'password')

        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8">
                <div class="card">
                    <div class="card-header pb-0"><h5>Change Password</h5></div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('settings.password.update') }}">
                            @csrf @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label">Current Password <span class="text-danger">*</span></label>
                                <input type="password"
                                       name="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       required>
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">New Password <span class="text-danger">*</span></label>
                                <input type="password"
                                       name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Min 8 characters"
                                       required>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Confirm New Password <span
                                        class="text-danger">*</span></label>
                                <input type="password"
                                       name="password_confirmation"
                                       class="form-control"
                                       required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    @endif

</x-app-layout>
