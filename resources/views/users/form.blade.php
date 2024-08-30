<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($user) ? 'Update' : 'Add' }} User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>{{ isset($user) ? 'Update' : 'Add' }} User</h2>
    <form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if(isset($user))
            @method('PUT')
        @endif

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', isset($user) ? $user->name : '') }}" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', isset($user) ? $user->email : '') }}" required>
        </div>

        <div class="form-group">
            <label for="mobile">Mobile:</label>
            <input type="text" name="mobile" id="mobile" class="form-control" value="{{ old('mobile', isset($user) ? $user->mobile : '') }}" required>
        </div>

        <div class="form-group">
            <label for="image">Profile Image (Max 10 MB):</label>
            <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
            @if(isset($user) && $user->image)
                <img src="{{ asset('storage/' . $user->image) }}" alt="Profile Image" class="mt-2" style="max-width: 100px;">
            @endif
        </div>

        <div class="form-group">
            <label for="country">Country:</label>
            <select name="country_id" id="country" class="form-control" required>
                <option value="">Select Country</option>
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" {{ old('country_id', isset($user) ? $user->country_id : '') == $country->id ? 'selected' : '' }}>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="form-group">
            <label for="state">State:</label>
            <select name="state_id" id="state" class="form-control" required>
                <option value="">Select State</option>
                @if(isset($states))
                    @foreach($states as $state)
                        <option value="{{ $state->id }}" {{ old('state_id', isset($user) ? $user->state_id : '') == $state->id ? 'selected' : '' }}>
                            {{ $state->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="form-group">
            <label for="city">City:</label>
            <select name="city_id" id="city" class="form-control" required>
                <option value="">Select City</option>
                @if(isset($cities))
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ old('city_id', isset($user) ? $user->city_id : '') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
        <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Update' : 'Add' }} User</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {
        $('#country').on('change', function() {
            var countryId = $(this).val();
            if (countryId) {
                $.ajax({
                    url: '/getStates/' + countryId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#state').empty();
                        $('#state').append('<option value="">Select State</option>');
                        $.each(data, function(key, value) {
                            $('#state').append('<option value="'+ key +'">'+ value +'</option>');
                        });
                    }
                });
            } else {
                $('#state').empty();
                $('#city').empty();
            }
        });

        $('#state').on('change', function() {
            var stateId = $(this).val();
            if (stateId) {
                $.ajax({
                    url: '/getCities/' + stateId,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#city').empty();
                        $('#city').append('<option value="">Select City</option>');
                        $.each(data, function(key, value) {
                            $('#city').append('<option value="'+ key +'">'+ value +'</option>');
                        });
                    }
                });
            } else {
                $('#city').empty();
            }
        });
    });
</script>
</body>
</html>
