<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vocab;
use Illuminate\View\View;

class VocabularyController extends Controller
{
    public function show():View {
        $vocabs = Vocab::all();
        return view('vocabs', ['vocabs' => $vocabs]);
    }
}
