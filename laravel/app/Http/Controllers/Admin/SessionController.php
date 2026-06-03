<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSessionRequest;
use App\Models\Session;
use App\Models\ClassRoom;
use Illuminate\Support\Str;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = Session::with(['classRoom', 'createdBy'])
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.sessions.index', compact('sessions'));
    }

    public function create()
    {
        $classes = ClassRoom::active()->orderBy('name')->get();
        return view('admin.sessions.create', compact('classes'));
    }

    public function store(StoreSessionRequest $request)
    {
        Session::create([
            ...$request->validated(),
            'code'       => 'SES-' . strtoupper(Str::random(6)),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Sesi presensi berhasil dibuat.');
    }

    public function show(Session $session)
    {
        $session->load(['classRoom', 'createdBy', 'attendances.student']);
        return view('admin.sessions.show', compact('session'));
    }

    public function edit(Session $session)
    {
        $classes = ClassRoom::active()->orderBy('name')->get();
        return view('admin.sessions.edit', compact('session', 'classes'));
    }

    public function update(StoreSessionRequest $request, Session $session)
    {
        $session->update($request->validated());

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Sesi berhasil diperbarui.');
    }

    public function monitor(Session $session)
    {
        $session->load('classRoom');
        return view('admin.sessions.monitor', compact('session'));
    }

    public function close(Session $session)
    {
        $session->update(['status' => 'closed']);

        return redirect()->back()
            ->with('success', 'Sesi berhasil ditutup.');
    }

    public function reopen(Session $session)
    {
        $session->update(['status' => 'open']);

        return redirect()->back()
            ->with('success', 'Sesi berhasil dibuka kembali.');
    }

    public function destroy(Session $session)
    {
        $session->delete();

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Sesi berhasil dihapus.');
    }
}
