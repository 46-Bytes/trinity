<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TrinityAi Diagnostic Summary and Advice</title>
    <style>
        body, td, li, p {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        table, th, td {
            /*border-collapse: collapse;*/
            /*table-layout: fixed;*/
            /* remove any “!*” style comments; use standard CSS comments only */
            border: 1px solid #444;
            padding: 6px 8px;
        }

        h1, h2, h3, h4, h5, h6 {
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .sub-table th, .sub-table td {
            font-size: 10px;
        }

        .page-break {
            page-break-after: always;
            /* newer engines also support: */
            break-after: page;
        }

    </style>
</head>
<body>
<h1>TrinityAi Diagnostic</h1>
<p><strong>User:</strong> {{ Auth::user()->name }}</p>
<p><strong>Date:</strong> {{ $diagnostic->created_at->format('F j, Y') }}</p>

<h2>Diagnostic Summary</h2>
<div class="summary">
    {!! convertMarkdownToHtml($diagnostic->summary) !!}
</div>


<div class="page-break"></div>
<h2>Diagnostic Advice</h2>
<div class="advice">
    {!! $diagnostic->advice !!}
</div>


<div class="page-break"></div>

<h2>Scoring</h2>
@php
    $raw = $diagnostic->json_scoring ?? '';
    $isJson = str_starts_with($raw, '{');
    $scoring = $isJson ? json_decode($raw) : null;
@endphp
<div class="scoring">
    @if($isJson)

        @if(isset($scoring->scored_rows))
            <h3>Scored Responses</h3>
            <table border="1" cellpadding="6" cellspacing="0"
                   style="border-collapse: collapse; width:auto; table-layout:auto;">
                <thead>
                <tr>
                    <th style="width:50%;">Question</th>
                    <th style="width:15%;">Response</th>
                    <th style="width:10%;">Score</th>
                    <th style="width:20%;">Module</th>
                </tr>
                </thead>
                <tbody>
                @foreach($scoring->scored_rows as $index => $row)
                    <tr>
                        <td>{{ $row->question }}</td>
                        <td>{{ $row->response }}</td>
                        <td>{{ $row->score }}</td>
                        <td>{{ $row->module }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif







        <h2>Client Summary</h2>
        @if(isset($scoring->clientSummary))
            {!! $scoring->clientSummary !!}
        @endif
        @if(isset($scoring->roadmap))
            <br>
            <h3>Roadmap</h3>

            <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; ">
                <thead>
                <tr>
                    <th>Module</th>
                    <th>RAG</th>
                    <th>Score</th>
                    <th>Rank</th>
                    <th>Why Priority</th>
                    <th>Quick Wins</th>
                </tr>
                </thead>
                <tbody>
                @foreach($scoring->roadmap as $index => $item)
                    <tr>
                        <td>{{ $item->module }}</td>
                        <td>{{ $item->rag }}</td>
                        <td>{{ $item->score }}</td>
                        <td>{{ $item->rank }}</td>
                        <td>{{ $item->whyPriority }}</td>
                        <td>{{ $item->quickWins }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif

        <div class="page-break"></div>
        {{--        @if(isset($scoring->clientResponses))--}}
        @if(isset($scoring->clientResponses->scored_rows))
            <h3>Scored Responses:</h3>
            <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; ">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Question</th>
                    <th>Response</th>
                    <th>Score</th>
                    <th>Module</th>
                </tr>
                </thead>
                <tbody>
                {{--                @foreach($scoring->clientResponses as $index => $resp)--}}
                @foreach($scoring->clientResponses->scored_rows as $index => $resp)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $resp->question }}</td>
                        <td>{{ $resp->response }}</td>
                        <td>{{ $resp->score }}</td>
                        <td>{{ $resp->module }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        @endif

    @else
        {!! $raw !!}
    @endif
</div>

@php
    $qas = json_decode($diagnostic->form_entry->getQAJson(), true) ?? [];

    // Inline “humanize” helper as a closure
    $humanLabel = fn(string $slug): string => ucwords(str_replace(['_', '-'], ' ', $slug));
@endphp

{{--<div class="page-break"></div>--}}
<h3>All Responses:</h3>
<table border="1" cellpadding="8" cellspacing="0" style="width:100%;table-layout: fixed;border-collapse: collapse;">
    <thead>
    <tr>
        <th style="width:5%;">#</th>
        <th style="width:45%;">Question</th>
        <th style="width:50%;">Response</th>
    </tr>
    </thead>
    <tbody>
    @php $i = 1; @endphp

    @foreach($qas as $question => $answer)
        @php
            // detect matrixdynamic: a list of row‑arrays
            $isMatrix =
              is_array($answer)
              && array_is_list($answer)
              && isset($answer[0])
              && is_array($answer[0]);
        @endphp

        @if($isMatrix)
            {{-- 1) question row --}}
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $humanLabel($question) }}</td>
                <td>See table below</td>
            </tr>
            {{-- 2) full-width matrixdynamic table --}}
            <tr>
                <td colspan="3" style="padding:0; border:none;">
                    <table class="resp-table sub-table"
                           border="1" cellpadding="4" cellspacing="0"
                           style="width:100%;table-layout: fixed;border-collapse: collapse;">
                        <thead>
                        <tr>
                            @php $cols = array_keys($answer[0]); @endphp
                            @foreach($cols as $col)
                                <th>{{ $humanLabel($col) }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($answer as $row)
                            @php
                                $hasData = collect($row)
                                           ->filter(fn($v) => $v !== null && $v !== '')
                                           ->isNotEmpty();
                            @endphp
                            @if($hasData)
                                <tr>
                                    @foreach($cols as $col)
                                        <td>
                                            {{ is_array($row[$col] ?? null)
                                                ? json_encode($row[$col])
                                                : ($row[$col] ?? '') }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>

        @else
            {{-- all other cases: your original 4‑case logic --}}
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $humanLabel($question) }}</td>
                <td>
                    {{-- 1) associative array? --}}
                    @if(is_array($answer) && ! array_is_list($answer))
                        <ul style="margin:0; padding-left:1.2em; list-style: disc;">
                            @foreach($answer as $key => $val)
                                <li>
                                    {{ $humanLabel($key) }}:
                                    <strong>{{ $val }}</strong>
                                </li>
                            @endforeach
                        </ul>

                        {{-- 2) simple list of scalars --}}
                    @elseif(is_array($answer))
                        <ul style="margin:0; padding-left:1.2em; list-style: disc;">
                            @foreach($answer as $val)
                                <li>{{ is_array($val) ? json_encode($val) : $val }}</li>
                            @endforeach
                        </ul>

                        {{-- 3) plain scalar --}}
                    @else
                        {{ $answer }}
                    @endif
                </td>
            </tr>
        @endif

    @endforeach
    </tbody>
</table>

</body>
</html>
