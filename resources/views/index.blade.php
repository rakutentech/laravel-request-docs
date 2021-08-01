<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>Laravel Request Docs</title>
      <meta name="description" content="">
      <meta name="keywords" content="">
      <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
   </head>
   <body class="bg-grey-lightest tracking-wide">

      <div class="w-full flex flex-wrap mx-auto px-2 pt-8 lg:pt-16">
         <div class="text-xl text-grey-darkest break-all">
            <h1>Routes List</h1>
            <table class="table-fixed text-sm">
                <tbody>
                    @foreach ($docs as $index => $doc)
                    <tr>
                        <td>
                            <a href="#{{$doc['uri']}}">
                                <span class="font-thin inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-{{$doc['methods'][0] == 'GET' ? 'green': 'red'}}-100 bg-{{$doc['methods'][0] == 'GET' ? 'green': 'red'}}-500 rounded">
                                    {{$doc['methods'][0]}}
                                </span>
                                <span class="font-mono">{{$doc['uri']}}</span>
                            </a>
                        <td>
                    </td>
                    @endforeach
                </tbody>
            </table>
         </div>
         <br><br>
         <div class="pl-2 w-2/3 text-black">
            @foreach ($docs as $index => $doc)
            <div class="font-sans" id="{{$doc['uri']}}">
               <h1 class="font-sans break-normal text-black pt-1 pb-2 ">
                <span class="font-thin">{{$doc['methods'][0]}}</span>
                <span class="font-mono">{{$doc['uri']}}</span>
               </h1>
            </div>
            <hr class="border-b border-grey-light">
            <table class="table-fixed align-center min-w-full text-sm mt-5">
                <thead class="border">
                  <tr class="border">
                    <th class="border pl-2 pr-16 pt-1 pb-1 w-12 text-left bg-{{in_array('GET', $doc['methods']) ? 'green' : 'red'}}-200">Attributes</th>
                    <th class="border pl-2 pr-16 pt-1 pb-1 w-12 text-left bg-{{in_array('GET', $doc['methods']) ? 'green' : 'red'}}-200">Required</th>
                    <th class="border pl-2 pr-16 pt-1 pb-1 w-10 text-left bg-{{in_array('GET', $doc['methods']) ? 'green' : 'red'}}-200">Type</th>
                    <th class="border pl-2 pr-16 pt-1 pb-1 w-1/20 text-left bg-{{in_array('GET', $doc['methods']) ? 'green' : 'red'}}-200">Rules</th>
                  </tr>
                </thead>
                <tbody>
                @foreach ($doc['rules'] as $attribute => $rules)
                  <tr class="border">
                    <td class="border pl-3 pt-1 pb-1 pr-2 bg-gray-100">{{$attribute}}</td>
                    <td class="border pl-3 pt-1 pb-1 pr-2">
                        @foreach ($rules as $rule)
                            @if (str_contains($rule, 'required'))
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-pink-100 bg-pink-600 rounded">REQUIRED</span>
                            @endif
                        <br>
                        @endforeach
                    </td>
                    <td class="border pl-3 pt-1 pb-1 pr-2 bg-gray-100">
                        @foreach ($rules as $rule)
                            @if (str_contains($rule, 'integer'))
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-blue-100 bg-blue-500 rounded">Integer</span>
                            @endif
                            @if (str_contains($rule, 'string'))
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-green-100 bg-green-500 rounded">String</span>
                            @endif
                            @if (str_contains($rule, 'array'))
                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-700 bg-yellow-400 rounded">Array</span>
                            @endif
                        <br>
                        @endforeach
                    </td>
                    <td class="border pl-3 pr-2">
                        <div class="font-mono">
                            @foreach ($rules as $rule)
                                {{ str_replace(["required|", "integer|", "string|"], ["", "", ""], $rule) }}
                            @endforeach
                        </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            <br><br>
            @endforeach
         </div>
      </div>
   </body>
</html>
