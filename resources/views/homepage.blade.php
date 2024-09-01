<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bunkai</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="http://takuyaa.github.io/kuromoji.js/demo/kuromoji/build/kuromoji.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container">
        <div class="mt-4">
            <button id="vocabs" class="btn btn-warning font-monospace">Vocabulary</button>
            <button id="test" class="btn btn-warning font-monospace" data-bs-toggle="modal" data-bs-target="#exampleModal">Test your Vocabulary</button>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h1 class="modal-title fs-5 font-monospace" id="exampleModalLabel">Question</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div id="modal-body" class="modal-body">
                            @isset($message)
                                <p class="font-monospace">{{$message}}</p>
                            @else
                                @isset($word)
                                <div class="fs-6 font-monospace mb-4">What is the meaning of {{$word}}?</div>
                                <input id="word" type="hidden" name="{{$word}}">
                                @endisset
                                @isset($options)
                                @foreach ($options as $i => $option)
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault{{$i}}">
                                    <label class="form-check-label" for="flexRadioDefault{{$i}}">
                                    {{$option}}
                                    </label>
                                </div>  
                                @endforeach
                                @endisset
                            @endisset
                        </div>
                        <div class="modal-footer">
                            @isset($test_result)
                            <p id="test-result" class="font-monospace fs-5 {{ $test_result == 'Correct Answer' ? 'text-success' : 'text-danger' }}">
                                {{ $test_result }}
                            </p>
                            @else
                                <p id="test-result"></p>
                            @endisset
                            <button type="button" id="submit-question" class="btn btn-warning font-monospace">Check Answer</button>
                            <button type="button" id="update-question" class="btn btn-warning font-monospace">Change Question</button>
                        </div>
                    </div>
                </div>
            </div>
            <form id="frmMain">
                <div class="mb-3 mt-4">
                    <label for="sentence" class="form-label font-monospace">Enter a sentence in Japanese</label>
                    <textarea class="form-control font-monospace" name="sentence" id="sentence" rows="3" placeholder="Example 私はスター"></textarea>
                </div>
                <p id="error-message" class="text-danger fs-6 font-monospace"></p>
                <button class="btn btn-warning font-monospace mb-3" type="submit" id="submit">Analyse Sentence</button>
                <div id="loading-spinner" class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </form>
            <div id="output">
                <div id="results">
                    @foreach ($results as $word => $meaning)
                        <p class="font-monospace">{{$word}} : {{$meaning}} 
                            <span>
                                @if (array_key_exists($word, $vocabs))
                                <svg id="{{$word}}" custom-action="delete" style="cursor: pointer;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                                </svg> 
                                @else
                                <svg id="{{$word}}" custom-action="add" style="cursor: pointer;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="blue" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
                                </svg>
                                @endif
                            </span>
                        </p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</body>
</html>