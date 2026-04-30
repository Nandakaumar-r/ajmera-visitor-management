<!DOCTYPE html>
<html>
<head>
    <title>Exit Interview</title>
</head>
<body>
    <h2>Exit Interview Questions</h2>
    <ul>
        @foreach($questions as $question)
            <li>
                {{ $question->question }}
                @if($question->field_type === 'radio' && $question->options)
                    <ul>
                        @foreach(json_decode($question->options, true) as $option)
                            <li>{{ $option }}</li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</body>
</html>
