<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\CustomerCreatedMail;
use App\Mail\CustomerAcceptedMail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AdminCustomerController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.customers.index', [
            'customers' => User::where('is_admin', false)->latest()->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorizeAdmin($request);

        return view('admin.customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'status' => ['required', 'in:active,refused'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
            'status' => $validated['status'],
        ]);

        try {
            Mail::to($user->email)->send(new CustomerCreatedMail($user, $validated['password']));
        } catch (\Exception $e) {
            logger()->error('Erreur d\'envoi email de creation client: ' . $e->getMessage());
        }

        return redirect()->route('admin.customers.index')->with('success', 'Client ajoute avec succes.');
    }

    public function edit(Request $request, User $customer): View
    {
        $this->authorizeAdmin($request);
        abort_if($customer->is_admin, 404);

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer): RedirectResponse
    {
        $this->authorizeAdmin($request);
        abort_if($customer->is_admin, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $customer->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'status' => ['required', 'in:active,refused'],
        ]);

        $oldStatus = $customer->status;

        $customer->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
        ]);

        if (!empty($validated['password'])) {
            $customer->password = Hash::make($validated['password']);
        }

        $customer->save();

        if ($oldStatus !== 'active' && $customer->status === 'active') {
            try {
                Mail::to($customer->email)->send(new CustomerAcceptedMail($customer));
            } catch (\Exception $e) {
                logger()->error('Erreur d\'envoi email d\'acceptation client dans update: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.customers.index')->with('success', 'Client modifie avec succes.');
    }

    public function accept(Request $request, User $customer): RedirectResponse
    {
        $this->authorizeAdmin($request);
        abort_if($customer->is_admin, 404);

        $oldStatus = $customer->status;
        $customer->update(['status' => 'active']);

        if ($oldStatus !== 'active') {
            try {
                Mail::to($customer->email)->send(new CustomerAcceptedMail($customer));
            } catch (\Exception $e) {
                logger()->error('Erreur d\'envoi email d\'acceptation client dans accept: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Client accepte.');
    }

    public function refuse(Request $request, User $customer): RedirectResponse
    {
        $this->authorizeAdmin($request);
        abort_if($customer->is_admin, 404);

        $customer->update(['status' => 'refused']);

        return back()->with('success', 'Client refuse.');
    }

    public function destroy(Request $request, User $customer): RedirectResponse
    {
        $this->authorizeAdmin($request);
        abort_if($customer->is_admin, 404);

        $customer->delete();

        return back()->with('success', 'Client supprime.');
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }
}
