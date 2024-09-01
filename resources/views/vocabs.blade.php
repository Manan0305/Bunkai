<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vocabs</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/vocab.js'])
</head>
<body>
    <div class="container">
        <button id="refresh" class="btn btn-warning font-monospace mt-3 mb-3">Refresh</button>
        <table id="vocabs" class="table table-light table-striped table-bordered">
            <thead>
              <tr>
                <th class="col-2" scope="col">Word</th>
                <th class="col-10" scope="col">Meaning</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($vocabs as $vocab)
                <tr>
                    <th class="col-2" scope="row">{{$vocab->word}}</th>
                    <td class="col-10">{{$vocab->meaning}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>