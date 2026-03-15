{{-- Shared by create.blade.php and edit.blade.php --}}
{{-- $client is available on edit, undefined on create --}}

<div class="row g-3">

    {{-- Name --}}
    <div class="col-12">
        <label class="form-label" for="name">
            Client Name <span class="text-danger">*</span>
        </label>
        <input type="text"
               id="name"
               name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $client->name ?? '') }}"
               placeholder="e.g. Acme Corporation"
               required
               autofocus>
        @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Email --}}
    <div class="col-md-6">
        <label class="form-label" for="email">Email Address</label>
        <input type="email"
               id="email"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $client->email ?? '') }}"
               placeholder="client@example.com">
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Phone --}}
    <div class="col-md-6">
        <label class="form-label" for="phone">Phone Number</label>
        <input type="text"
               id="phone"
               name="phone"
               class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone', $client->phone ?? '') }}"
               placeholder="+254 7XX XXX XXX">
        @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Address --}}
    <div class="col-12">
        <label class="form-label" for="address">Address</label>
        <textarea id="address"
                  name="address"
                  class="form-control @error('address') is-invalid @enderror"
                  rows="3"
                  placeholder="Street, City, Country">{{ old('address', $client->address ?? '') }}</textarea>
        @error('address')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>
