<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>LRD</title>
      <meta name="description" content="Laravel Request Docs">
      <meta name="keywords" content="">
      <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
      <script src="https://unpkg.com/vue-prism-editor"></script>
      <script src="https://unpkg.com/axios@0.2.1/dist/axios.min.js"></script>
      <link rel="stylesheet" href="https://unpkg.com/vue-prism-editor/dist/prismeditor.min.css" />

      <script src="https://unpkg.com/prismjs/prism.js"></script>
      <link rel="stylesheet" href="https://unpkg.com/prismjs/themes/prism-tomorrow.css" />

      <script src="https://unpkg.com/faker@5.5.3/dist/faker.min.js" referrerpolicy="no-referrer"></script>
      <style>
        [v-cloak] {
            display: none;
        }

        .request-editor {
            /* we dont use `language-` classes anymore so thats why we need to add background and text color manually */
            background: #2d2d2d;
            color: #ccc;

            /* you must provide font-family font-size line-height. Example:*/
            font-family: Fira code, Fira Mono, Consolas, Menlo, Courier, monospace;
            font-size: 12px;
            line-height: 1.5;
            padding: 5px;
        }

        /* optional class for removing the outline */
        .prism-editor__textarea:focus {
            outline: none;
        }
      </style>
   </head>
   <body class="bg-gray-100 tracking-wide bg-gray-200">

        <nav class="bg-white py-2 ">
            <div class="container px-4 mx-auto md:flex md:items-center">

            <div class="flex justify-between items-center">
                <a href="{{config('request.docs.url')}}" class="font-bold text-xl text-indigo-600">LRD</a>
            </div>

                <div class="hidden md:flex flex-col md:flex-row md:ml-auto mt-3 md:mt-0" id="navbar-collapse">

                    <a href="https://github.com/rakutentech/laravel-request-docs" class="p-2 lg:px-4 md:mx-2 text-indigo-600 text-center border border-solid border-indigo-600 rounded hover:bg-indigo-600 hover:text-white transition-colors duration-300 mt-1 md:mt-0 md:ml-1">Github</a>
                </div>
            </div>
        </nav>
      <div id="app" v-cloak class="w-full flex lg:pt-10">
         <aside class="text-xl text-grey-darkest break-all bg-gray-200 pl-2 h-screen sticky top-1 overflow-auto">
            <h1>Routes List</h1>
            <hr class="border-b border-gray-300">
            <table class="table-fixed text-sm mt-5">
                <tbody>
                    @foreach ($docs as $index => $doc)
                    <tr>
                        <td>
                            <a href="#{{$doc['methods'][0] .'-'. $doc['uri']}}">
                                <span class="w-20
                                    font-thin
                                    inline-flex
                                    items-center
                                    justify-center
                                    px-2
                                    py-1
                                    text-xs
                                    font-bold
                                    leading-none
                                    rounded
                                    text-{{in_array('GET', $doc['methods']) ? 'green': ''}}-100 bg-{{in_array('GET', $doc['methods']) ? 'green': ''}}-500
                                    text-{{in_array('POST', $doc['methods']) ? 'black': ''}}-100 bg-{{in_array('POST', $doc['methods']) ? 'red': ''}}-500
                                    text-{{in_array('PUT', $doc['methods']) ? 'black': ''}}-100 bg-{{in_array('PUT', $doc['methods']) ? 'yellow': ''}}-500
                                    text-{{in_array('DELETE', $doc['methods']) ? 'white': ''}} bg-{{in_array('DELETE', $doc['methods']) ? 'black': ''}}
                                    ">
                                    {{$doc['methods'][0]}}
                                </span>
                                <span class="text-xs">{{$doc['uri']}}</span>
                            </a>
                        <td>
                    </td>
                    @endforeach
                </tbody>
            </table>
        </aside>
         <br><br>
         <div class="ml-6 mr-6 pl-2 w-2/3 bg-gray-300 p-2">
            @foreach ($docs as $index => $doc)
            <section class="pt-5 pl-2 pb-5 border mb-10 rounded bg-white shadow">
                <div class="font-sans" id="{{$doc['methods'][0] .'-'. $doc['uri']}}">
                <h1 class="font-sans break-normal text-black pt-1 pb-2 ">
                    <span class="w-20
                        font-thin
                        inline-flex
                        items-center
                        justify-center
                        px-2
                        py-1
                        text-xs
                        font-bold
                        leading-none
                        rounded
                        text-{{in_array('GET', $doc['methods']) ? 'green': ''}}-100 bg-{{in_array('GET', $doc['methods']) ? 'green': ''}}-500
                        text-{{in_array('POST', $doc['methods']) ? 'black': ''}}-100 bg-{{in_array('POST', $doc['methods']) ? 'red': ''}}-500
                        text-{{in_array('PUT', $doc['methods']) ? 'black': ''}}-100 bg-{{in_array('PUT', $doc['methods']) ? 'yellow': ''}}-500
                        text-{{in_array('DELETE', $doc['methods']) ? 'white': ''}} bg-{{in_array('DELETE', $doc['methods']) ? 'black': ''}}
                        ">
                        {{$doc['methods'][0]}}
                    </span>
                    <span class="">
                        <a href="#{{$doc['uri']}}">{{$doc['uri']}}</a>
                    </span>
                </h1>
                </div>
                <hr class="border-b border-grey-light">

                <table class="table-fixed text-sm mt-5 shadow-inner">
                    <thead class="border">
                    </thead>
                    <tbody>
                        <tr>
                            <td class="align-left border pl-2 pr-2 bg-gray-200 font-bold">Controller</td>
                            <td class="align-left border pl-2 pr-2 break-all">{{$doc['controller']}}</td>
                        </tr>
                        <tr>
                            <td class="align-left border pl-2 pr-2 bg-gray-200 font-bold">Function</td>
                            <td class="align-left border pl-2 pr-2 break-all">{{$doc['method']}}</td>
                        </tr>
                        @foreach ($doc['middlewares'] as $middleware)
                            <tr>
                                <td class="align-left border pl-2 pr-2 bg-gray-200">Middleware</td>
                                <td class="align-left border pl-2 pr-2 break-all">{{$middleware}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if (!empty($doc['rules']))
                <table class="table-fixed align-left min-w-full text-sm mt-5">
                    <thead class="border">
                    <tr class="border">
                        <th class="border pl-2 pr-16 pt-1 pb-1 w-12 text-left bg-gray-200">Attributes</th>
                        <th class="border pl-2 pr-16 pt-1 pb-1 w-12 text-left bg-gray-200">Required</th>
                        <th class="border pl-2 pr-16 pt-1 pb-1 w-10 text-left bg-gray-200">Type</th>
                        <th class="border pl-2 pr-16 pt-1 pb-1 w-1/20 text-left bg-gray-200">Rules</th>
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
                                @if (str_contains($rule, 'date'))
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-800 bg-yellow-400 rounded">Date</span>
                                @endif
                            <br>
                            @endforeach
                        </td>
                        <td class="border pl-3 pr-2 break-all">
                            <div class="font-mono">
                                @foreach ($rules as $rule)
                                    {{-- No print on just one rule 'required', as already printed as label --}}
                                    @if ($rule != 'required')
                                        {{ str_replace(["required|", "integer|", "string|"], ["", "", ""], $rule) }}
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
                <button class="bg-transparent hover:bg-indigo-500 text-indigo-700 font-semibold hover:text-white mt-2 pt-1 pb-1 pl-10 pr-10 border border-indigo-500 hover:border-transparent shadow-inner rounded"
                    v-if="!docs[{{$index}}]['try']" v-on:click="docs[{{$index}}]['try'] = !docs[{{$index}}]['try']">Try</button>
                <button v-if="docs[{{$index}}]['try']" @click="request(docs[{{$index}}])" class="bg-red-500 hover:bg-red-700 text-white font-bold mt-2 pt-1 pb-2 mb-1 pl-10 pr-10 shadow-xl rounded">
                    <svg v-if="docs[{{$index}}]['loading']" class="animate-spin h-4 w-4 rounded-full bg-transparent border-2 border-transparent border-opacity-50 inline pr-2" style="border-right-color: white; border-top-color: white;" viewBox="0 0 24 24"></svg> Run
                </button>
                <div class="grid grid-cols-1 mt-3 pr-2">
                    <div class="">
                        <div v-if="docs[{{$index}}]['try']">
                            <h3 class="font-thin">REQUEST URL</h3>
                            <prism-editor class="request-editor" style="min-height:35px" v-model="docs[{{$index}}]['url']" :highlight="highlighter"></prism-editor>
                            <br>
                            @if (!in_array('GET', $doc['methods']))
                            <h3 class="font-thin">REQUEST BODY</h3>
                            <prism-editor class="request-editor" style="min-height:200px;" v-model="docs[{{$index}}]['body']" :highlight="highlighter" line-numbers></prism-editor>
                            @endif
                        </div>
                    </div>
                    <div class="">
                        <div v-if="docs[{{$index}}]['response']">
                            <h3 class="font-bold mb-2 mt-2">
                                RESPONSE
                                <span v-if="docs[{{$index}}]['responseOk']" class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-green-100 bg-green-500 rounded">OK</span>
                                <span v-if="!docs[{{$index}}]['responseOk']" class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-500 rounded">ERROR</span>
                            </h3>
                            <prism-editor v-if="docs[{{$index}}]['response']" class="request-editor" style="min-height:200px;max-height:500px;background:#282525" v-model="docs[{{$index}}]['response']" :highlight="highlighter" line-numbers></prism-editor>
                        </div>
                    </div>
                  </div>
            </section>
            @endforeach
         </div>
      </div>
      <script>
        var guessValue = function(attribute, rules) {
            //console.log(attribute)
            //console.log(rules)
            // match max:1
            var validations = {
                max: 100,
                min: 1,
                isInteger: null,
                isString: null,
                isArray: null,
                isDate: null,
                value: '',
            }
            rules.map(function(rule) {
                validations.isRequired = rule.match(/required/)
                validations.isDate = rule.match(/date/)
                validations.isArray = rule.match(/array/)
                validations.isString = rule.match(/string/)
                if (rule.match(/integer/)) {
                    validations.isInteger = true
                }
                if (rule.match(/min:([0-9]+)/)) {
                    validations.min = rule.match(/min:([0-9]+)/)[1]
                }
                if (rule.match(/max:([0-9]+)/)) {
                    validations.max = rule.match(/max:([0-9]+)/)[1]
                }
            })

            if (validations.isString) {
                validations.value = faker.name.findName()
            }

            if (validations.isInteger) {
                validations.value = faker.datatype.number({ min: validations.min, max:validations.max })
            }
            if (validations.isDate) {
                validations.value = new Date(faker.datatype.datetime()).toISOString().slice(0, 10)
            }

            return validations
        }
        var docs = {!! json_encode($docs) !!}
        var app_url = {!! json_encode(config('app.url')) !!}
        //remove trailing slash if any
        app_url = app_url.replace(/\/$/, '')
        docs.map(function(doc, index) {
            doc.response = null
            doc.responseOk = null
            doc.body = "{}"
            doc.url = app_url + "/"+ doc.uri
            doc.try = false
            doc.loading = false
            // check in array
            if (doc.methods[0] == 'GET') {
                var idx = 1
                Object.keys(doc.rules).map(function(attribute) {
                    // check contains in string
                    if (attribute.indexOf('.*') !== -1) {
                        return
                    }
                    var value = guessValue(attribute, doc.rules[attribute])
                    if (!value.isRequired) {
                        //return
                    }

                    if (idx === 1) {
                        doc.url += "\n"+ "?"+attribute+"="+value.value+"\n"
                    } else {
                        doc.url += "&"+attribute+"="+value.value+"\n"
                    }
                    idx++
                })
            }

            // assume to be POST, PUT, DELETE
            if (doc.methods[0] != 'GET') {
                body = {}
                Object.keys(doc.rules).map(function(attribute) {
                    // ignore the child attributes
                    if (attribute.indexOf('.*') !== -1) {
                        return
                    }
                    body[attribute] = guessValue(attribute, doc.rules[attribute]).value
                })
                doc.body = JSON.stringify(body, null, 2)
            }

        })
        var app = new Vue({
            el: '#app',
            data: {
              docs: docs
            },
            methods: {
                highlighter(code) {
                    // js highlight example
                    return Prism.highlight(code, Prism.languages.js, "js");
                },
                request(doc) {
                    // convert string to lower case
                    var method = doc['methods'][0].toLowerCase()
                    // remove \n from string that is used for display
                    var url = doc.url.replace(/\n/g, '')

                    try {
                        var json = JSON.parse(doc.body.replace(/\n/g, ''))
                    } catch (e) {
                        doc.response = "Cannot parse JSON request body"
                        return
                    }
                    doc.loading = true
                    var config = {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }
                    axios({
                        method: method,
                        url: url,
                        data: json,
                        withCredentials: true,
                        config: config
                      }).then(function (response) {
                        doc.response = JSON.stringify(response, null, 2)
                        doc.responseOk = true
                      }).catch(function (error) {
                        doc.response = JSON.stringify(error, null, 2)
                        doc.responseOk = false
                      }).then(function () {
                        doc.loading = false
                      })
                }
            },
          });
      </script>
   </body>
</html>
