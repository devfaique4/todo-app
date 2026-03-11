<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return redirect()->route('projects.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:7',
            'icon'        => 'nullable|string|max:50',
        ]);

        Project::create([
            'name'        => $request->name,
            'description' => $request->description,
            'color'       => $request->color ?? '#6c63ff',
            'icon'        => $request->icon ?? 'folder',
            'status'      => 'active',
        ]);

        return redirect()->route('projects.index')
                         ->with('success', 'Project created! 📁');
    }

    public function show(Project $project)
    {
        return redirect()->route('projects.index');
    }

    public function edit(Project $project)
    {
        return redirect()->route('projects.index');
    }

    public function update(Request $request, Project $project)
    {
        return redirect()->route('projects.index');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')
                         ->with('success', 'Project deleted');
    }
}