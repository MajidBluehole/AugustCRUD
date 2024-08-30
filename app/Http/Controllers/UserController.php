<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Sorting and Searching
        $query = User::query();
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('email', 'like', '%' . $request->input('search') . '%');
        }
        $users = $query->orderBy($request->input('sort', 'name'))->paginate(10);
        $countries = Country::all(); // Fetch all countries

        return view('users.index', compact('users','countries'));
    }

    public function create()
    {
        $countries = Country::all(); // Fetch all countries
        return view('users.create', compact('countries'));
    }
    

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'mobile' => 'required|string|max:15',
                'image' => 'nullable|image|max:10240', // 10 MB limit
                'country_id' => 'required|exists:countries,id',
                'state_id' => 'required|exists:states,id',
                'city_id' => 'required|exists:cities,id',
            ]);
    
            // Handle file upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
            } else {
                $imagePath = null;
            }
    
            User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'image' => $imagePath,
                'country_id' => $request->input('country_id'),
                'state_id' => $request->input('state_id'),
                'city_id' => $request->input('city_id'),
                'password' => bcrypt('password'),
            ]);
    
            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating user: ' . $e->getMessage());
    
            // Redirect with error message
            return redirect()->back()->with('error', 'There was an error creating the user. Please try again.');
        }
    }
    
    

    public function edit(User $user)
    {
        $countries = Country::all(); // Fetch all countries
        $states = State::all(); // Key-value pairs: id => name
        $cities = City::all(); // Key-value pairs: id => name
        return view('users.edit', compact('user', 'countries', 'states', 'cities'));
    }
    
    

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'mobile' => 'required|string|max:15',
            'image' => 'nullable|image|max:10240', // 10 MB limit
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
            $user->image = $imagePath;
        }

        $user->update($request->except('image'));

        $countries = Country::all(); // Fetch all countries
        $states = State::where('country_id', $request->input('country_id'))->pluck('name', 'id');
        $cities = City::where('state_id', $request->input('state_id'))->pluck('name', 'id');
        return redirect()->route('users.index')->with('success', 'User updated successfully.');    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
    public function getStates($country_id)
{
    $states = State::where('country_id', $country_id)->pluck('name', 'id');
    return response()->json($states);
}

public function getCities($state_id)
{
    $cities = City::where('state_id', $state_id)->pluck('name', 'id');
    return response()->json($cities);
}

}
