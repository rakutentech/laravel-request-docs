<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>{{ config('request-docs.document_name') }}</title>
      <meta name="description" content="Laravel Request Docs">
      <meta name="keywords" content="">
      <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
      <script src="https://unpkg.com/vue-prism-editor"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.16.2/axios.min.js"></script>
      <link rel="stylesheet" href="https://unpkg.com/vue-prism-editor/dist/prismeditor.min.css" />

      <script src="https://unpkg.com/prismjs/prism.js"></script>
      <link rel="stylesheet" href="https://unpkg.com/prismjs/themes/prism-tomorrow.css" />

      <script src="https://unpkg.com/faker@5.5.3/dist/faker.min.js" referrerpolicy="no-referrer"></script>
      <script src="https://unpkg.com/string-similarity@4.0.2/umd/string-similarity.min.js" referrerpolicy="no-referrer"></script>

      <script src="https://unpkg.com/vue-markdown@2.2.4/dist/vue-markdown.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/sql-formatter/3.1.0/sql-formatter.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
      {{-- <script src="{{asset('./vendor/request-docs/app.js')}}"></script> --}}
      <style>
        [v-cloak] {
            display: none;
        }
        a {
            color: #3f3398;
        }

        .my-prism-editor {
            /* you must provide font-family font-size line-height. Example:*/
            font-family: Fira code, Fira Mono, Consolas, Menlo, Courier, monospace;
            font-size: 12px;
            line-height: 1.25;
            padding: 5px;
        }

        /* optional class for removing the outline */
        .prism-editor__textarea:focus {
            outline: none;
        }
        .dropdown{
            position: relative;
            width: 100%;
        }
        .dropdown-input, .dropdown-selected{
            width: 100%;
            font-size:14px;
            padding: 10px 16px;
            border: 1px solid transparent;
            background: #edf2f7;
            outline: none;
            border-radius: 8px;
        }
        .dropdown-input:focus, .dropdown-selected:hover{
            background: #fff;
            border-color: #e2e8f0;
        }
        .dropdown-input::placeholder{
            opacity: 0.7;
        }
        .dropdown-selected{
            cursor: pointer;
        }
        .dropdown-list{
            z-index: 9999;
            position: absolute;
            width: 100%;
            max-height: 500px;
            margin-top: 4px;
            overflow-y: auto;
            background: #ffffff;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }
        .dropdown-item{
            display: flex;
            width: 100%;
            padding: 2px 6px;
            cursor: pointer;
            font-size: 12px;
        }
        .dropdown-item:hover{
            background: #edf2f7;
        }
      </style>
   </head>
   <body class="bg-gray-100 tracking-wide bg-gray-200">

        <nav class="bg-white py-2 ">
            <div class="container px-4 md:flex md:items-center">
                <div class="flex justify-between items-center">
                    <a href="{{config('request-docs.url')}}" class="font-bold text-xl text-indigo-600">{{ config('request-docs.document_name') }}</a>
                </div>
            </div>
        </nav>
      <div id="app" v-cloak class="w-full flex lg:pt-10">
         <aside class="text-sm ml-1.5 text-grey-darkest break-all bg-gray-200 pl-2 h-screen sticky top-1 overflow-auto">
            <h1 class="font-medium mx-3">Routes List</h1>
            <hr class="border-b border-gray-300">
            <table class="table-fixed text-sm mt-5" style="width: max-content">
                <tbody>
                    @foreach ($docs as $index => $doc)
                    <tr>
                        <td>
                            <a href="#{{$doc['methods'][0] .'-'. $doc['uri']}}" @click="highlightSidebar({{$index}})">
                                <span class="
                                    font-medium
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
                                    text-{{in_array('POST', $doc['methods']) ? 'black': ''}} bg-{{in_array('POST', $doc['methods']) ? 'red': ''}}-500
                                    text-{{in_array('PUT', $doc['methods']) ? 'black': ''}}-100 bg-{{in_array('PUT', $doc['methods']) ? 'yellow': ''}}-500
                                    text-{{in_array('PATCH', $doc['methods']) ? 'black': ''}}-100 bg-{{in_array('PATCH', $doc['methods']) ? 'yellow': ''}}-500
                                    text-{{in_array('DELETE', $doc['methods']) ? 'white': ''}} bg-{{in_array('DELETE', $doc['methods']) ? 'black': ''}}
                                    ">
                                    {{$doc['methods'][0]}}
                                </span>
                                <span class="text-xs" v-bind:class="docs[{{$index}}]['isActiveSidebar'] ? 'font-bold':''">
                                    <span class="text-gray-800 pr-1 pl-1" v-if="docs[{{$index}}]['responseOk'] === null">{{$doc['uri']}}</span>
                                    <span class="font-bold text-green-600 border rounded-full pr-1 pl-1 border-green-600" v-if="docs[{{$index}}]['responseOk'] === true">{{$doc['uri']}} - SUCCESS</span>
                                    <span class="font-bold text-red-600 border rounded-full pr-1 pl-1 border-red-500" v-if="docs[{{$index}}]['responseOk'] === false">{{$doc['uri']}} - ERROR</span>
                                </span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </aside>
         <br><br>
         <div class="ml-6 mr-6 pl-2 w-2/3 p-2" style="width: 100%">
            <h1 class="pl-2 pr-2 break-normal text-black break-normal font-sans text-black font-medium">
                Settings
            </h1>
            <section class="pt-5 pl-2 pr-2 pb-5 border mb-10 rounded bg-white shadow">
                <div class="font-sans" id="{{$doc['methods'][0] .'-'. $doc['uri']}}">
                    <h2 class="text-sm break-normal text-black break-normal font-sans pb-1 pt-1 text-black">
                        Append Request Headers
                    </h2>
                    <p class="text-xs pb-2 font-medium text-gray-500">Default headers sent on every request. Format <code>Key:Value</code></p>
                    <prism-editor
                        class="my-prism-editor"
                        style="min-height:100px;background:#2d2d2d;color: #ccc;resize:both" v-model="requestHeaders" :highlight="highlighter" line-numbers></prism-editor>
                </div>
            </section>
            <h1 class="pl-2 pr-2 break-normal text-black break-normal font-sans text-black font-medium">
                Routes List
            </h1>
            @foreach ($docs as $index => $doc)
            <section class="pt-5 pl-2 pr-2 pb-5 border mb-10 rounded bg-white shadow">
                <div class="font-sans" id="{{$doc['methods'][0] .'-'. $doc['uri']}}">
                <h1 class="text-sm break-normal text-black bg-indigo-50 break-normal font-sans pb-1 pt-1 text-black">
                    <span class="w-20
                        font-medium
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
                        text-{{in_array('POST', $doc['methods']) ? 'black': ''}} bg-{{in_array('POST', $doc['methods']) ? 'red': ''}}-500
                        text-{{in_array('PUT', $doc['methods']) ? 'black': ''}}-100 bg-{{in_array('PUT', $doc['methods']) ? 'yellow': ''}}-500
                        text-{{in_array('PATCH', $doc['methods']) ? 'black': ''}}-100 bg-{{in_array('PATCH', $doc['methods']) ? 'yellow': ''}}-500
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
                            <td class="align-left border border-gray-300 pl-2 pr-2 bg-gray-200 font-bold">Controller</td>
                            <td class="align-left border pl-2 pr-2 break-all">{{$doc['controller_full_path']}}</td>
                        </tr>
                        <tr>
                            <td class="align-left border border-gray-300 pl-2 pr-2 bg-gray-200 font-bold">Method</td>
                            <td class="align-left border pl-2 pr-2 break-all">{{"@" .$doc['method']}}</td>
                        </tr>
                        @foreach ($doc['middlewares'] as $middleware)
                            <tr>
                                <td class="align-left border border-gray-300 pl-2 pr-2 bg-gray-200">Middleware</td>
                                <td class="align-left border pl-2 pr-2 break-all">{{$middleware}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div v-if="docs[{{$index}}]['docBlock']" class="border-2 mr-4 mt-4 p-4 rounded shadow-inner text-sm">
                    <vue-markdown>{!! $doc['docBlock'] !!}</vue-markdown>
                </div>
                <br>
                @if (!empty($doc['rules']))
                <table class="table-fixed align-left text-sm mt-5">
                    <thead class="border">
                    <tr class="border">
                        <th class="border border-gray-300 pl-2 pr-16 pt-1 pb-1 w-12 text-left bg-gray-200">Attributes</th>
                        <th class="border border-gray-300 pl-2 pr-16 pt-1 pb-1 w-12 text-left bg-gray-200">Required</th>
                        <th class="border border-gray-300 pl-2 pr-16 pt-1 pb-1 w-10 text-left bg-gray-200">Type</th>
                        <th class="border border-gray-300 pl-2 pr-16 pt-1 pb-1 w-1/20 text-left bg-gray-200">Rules</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($doc['rules'] as $attribute => $rules)
                    <tr class="border">
                        <td class="border border-blue-200 pl-3 pt-1 pb-1 pr-2 bg-blue-100">{{$attribute}}</td>
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
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-800 bg-yellow-400 rounded">Array</span>
                                @endif
                                @if (str_contains($rule, 'date'))
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-800 bg-yellow-400 rounded">Date</span>
                                @endif
                                @if (str_contains($rule, 'boolean'))
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-800 bg-green-400 rounded">Boolean</span>
                                @endif
                            <br>
                            @endforeach
                        </td>
                        <td class="border pl-3 pr-2 break-all">
                            <div class="font-mono">
                                @foreach ($rules as $rule)
                                    {{-- No print on just one rule 'required', as already printed as label --}}
                                    @if($rule != 'required')
                                        <span>
                                            {{ str_replace(["required|", "integer|", "string|", "boolean|"], ["", "", ""], $rule) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
                <button
                    class="hover:bg-red-500 font-semibold hover:text-white mt-2 pl-5 pr-5 border-gray-700 hover:border-transparent shadow-inner border-2 rounded-full"
                    v-if="!docs[{{$index}}]['try']"
                    v-on:click="docs[{{$index}}]['try'] = !docs[{{$index}}]['try'];docs[{{$index}}]['cancel'] = !docs[{{$index}}]['cancel']"
                >
                    Try
                </button>

                <button
                    class="hover:bg-red-500 font-semibold hover:text-white mt-2 pl-5 pr-5 border-gray-700 hover:border-transparent shadow-inner border-2 rounded-full"
                    v-if="!docs[{{$index}}]['cancel']"
                    v-on:click="docs[{{$index}}]['cancel'] = !docs[{{$index}}]['cancel'];docs[{{$index}}]['try'] = !docs[{{$index}}]['try'];"
                >
                    Cancel
                </button>

                <button
                    v-if="docs[{{$index}}]['try']"
                    @click="request(docs[{{$index}}])"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold mt-2 border-blue-800 border-2 shadow-inner mb-1 pl-5 pr-5 rounded-full"
                >
                    <svg
                        v-if="docs[{{$index}}]['loading']"
                        class="animate-spin h-4 w-4 rounded-full bg-transparent border-2 border-transparent border-opacity-50 inline pr-2" style="border-right-color: white; border-top-color: white;" viewBox="0 0 24 24"></svg>
                    Run
                </button>


                <div v-if="docs[{{$index}}]['bearer'] && docs[{{$index}}]['try']" class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="token">
                        Bearer Token
                    </label>
                    <input
                        v-model="token"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="token"
                        type="text"
                        placeholder="Bearer Token"
                        autocomplete="off"
                    />
                </div>

                <div class="grid grid-cols-1 mt-3 pr-2 overflow-auto">
                    <div class="">
                        <div v-if="docs[{{$index}}]['try']">
                            <h3 class="font-medium">REQUEST URL<sup class="text-red-500 font-bold"> *required</sup></h3>
                            <p class="text-xs pb-2 font-medium text-gray-500">Enter your request URL with query params</p>
                            <prism-editor class="my-prism-editor"
                            style="padding:15px 0px 15px 0px;min-height:20px;background:#2d2d2d;color: #ccc;resize:both;"
                            v-model="docs[{{$index}}]['url']" :highlight="highlighter" line-numbers></prism-editor>
                            <br>
                            @if (!in_array('GET', $doc['methods']))
                            <h3 class="font-medium">REQUEST BODY<sup class="text-red-500"> *required</sup></h3>
                            <p class="text-xs pb-2 font-medium text-gray-500">JSON body for the POST|PUT|DELETE request</p>
                            <prism-editor
                                class="my-prism-editor"
                                style="min-height:200px;background:#2d2d2d;color: #ccc;resize:both"
                                v-model="docs[{{$index}}]['body']"
                                :highlight="highlighter"
                                line-numbers></prism-editor>
                            @endif
                        </div>
                    </div>
                    <div class="">
                        <div v-if="docs[{{$index}}]['response'] && !docs[{{$index}}]['cancel']">
                            <hr class="border-b border-dotted mt-4 mb-2 border-gray-300">
                            <h3 class="font-medium">
                                RESPONSE
                                <span
                                    v-if="docs[{{$index}}]['responseOk']"
                                    class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-green-100 bg-green-500 rounded">
                                    SUCCESS
                                </span>
                                <span
                                    v-if="!docs[{{$index}}]['responseOk']"
                                    class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-500 rounded">
                                    ERROR
                                </span>
                                <span
                                    v-if="docs[{{$index}}]['responseOk']"
                                    class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-green-100 bg-green-500 rounded"
                                    v-text="'STATUS CODE: ' + docs[{{$index}}]['responseCode']">
                                </span>
                                <span
                                    v-if="!docs[{{$index}}]['responseOk']"
                                    class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-500 rounded"
                                    v-text="'STATUS CODE: ' + docs[{{$index}}]['responseCode']">
                                </span>
                                <span
                                    v-if="docs[{{$index}}]['responseTime']"
                                    class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-800 bg-yellow-400 rounded"
                                    v-text="'Response time: ' + docs[{{$index}}]['responseTime'] + 'ms'">
                                </span>
                            </h3>
                            <p class="text-xs pb-2 font-medium text-gray-500">Response from the server</p>
                            <prism-editor
                                v-if="docs[{{$index}}]['response']"
                                class="my-prism-editor shadow-inner border-gray-400 border-2 rounded"
                                style="min-height:100px;max-height:300px;background:#2d2d2d;color: #ccc;resize:both"
                                readonly
                                v-model="docs[{{$index}}]['response']"
                                :highlight="highlighter"
                                line-numbers></prism-editor>
                            <div class="mt-2">
                                <h3 class="font-medium">
                                    SQL
                                </h3>
                                <p v-if="docs[{{$index}}]['queries'].length" class="text-xs pb-2 font-medium text-gray-500">
                                        SQL query log executed for this request.
                                </p>
                                <p v-if="!docs[{{$index}}]['queries'].length" class="text-xs pb-2 font-medium text-gray-500">No sql queries for this request</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <div v-for="(query, index) in docs[{{$index}}]['queries']">
                                        <p class="text-sm font-medium">@{{index+1}}.
                                            <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-800 bg-yellow-500 rounded mb-1">Query time: @{{query.time}}ms</span></p>
                                        <prism-editor
                                            v-model="sqlFormatter.format(query['sql'])"
                                            class="my-prism-editor"
                                            style="padding:10px 0px 10px 0px;min-height:20px;max-height:350px;background:rgb(52 33 33);color: #ccc;resize:both;"
                                            readonly
                                            :highlight="highlighter"
                                            line-numbers></prism-editor>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>

                  <button
                  v-if="docs[{{$index}}]['response']"
                  @click="request(docs[{{$index}}])"
                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold mt-2 border-blue-800 border-2 shadow-inner mb-1 pl-5 pr-5 rounded-full"
              >
                  <svg
                      v-if="docs[{{$index}}]['loading']"
                      class="animate-spin h-4 w-4 rounded-full bg-transparent border-2 border-transparent border-opacity-50 inline pr-2" style="border-right-color: white; border-top-color: white;" viewBox="0 0 24 24"></svg>
                  Re Run
                </button>
            </section>
            @endforeach
         </div>
      </div>
      <script>
        var guessValue = function(attribute, rules) {
            // match max:1
            var validations = {
                max: 100,
                min: 1,
                isInteger: null,
                isString: null,
                isArray: null,
                isDate: null,
                isIn: null,
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
                    if (!validations.min) {
                        validations.min = 1
                    }
                }
                if (rule.match(/max:([0-9]+)/)) {
                    validations.max = rule.match(/max:([0-9]+)/)[1]
                    if (!validations.max) {
                        validations.max = 100
                    }
                }
            })

            if (validations.isString) {
                validations.value = faker.name.findName()
            }
            if (validations.isInteger) {
                validations.value = Math.floor(Math.random() * (validations.max - validations.min + 1) + validations.min)
            }
            if (validations.isDate) {
                validations.value = new Date(faker.date.between(new Date(), new Date())).toISOString().slice(0, 10)
            }

            return validations
        }
        var docs = {!! json_encode($docs) !!};
        var app_url = {!! json_encode(config('app.url')) !!};

        //remove trailing slash if any
        app_url = app_url.replace(/\/$/, '')
        docs.map(function(doc, index) {
            doc.response = null
            doc.responseCode = 200
            doc.queries = []
            doc.responseOk = null
            doc.body = "{}"
            doc.isActiveSidebar = window.location.hash.substr(1) === doc['methods'][0] +"-"+ doc['uri']
            doc.url = app_url + "/"+ doc.uri
            doc.try = false
            doc.cancel = true
            doc.loading = false
            doc.responseTime = null
            // check in array
            if (doc.methods[0] == 'GET') {
                var idx = 1
                Object.keys(doc.rules).map(function(attribute) {
                    // check contains in string
                    if (attribute.indexOf('.*') !== -1) {
                        return
                    }
                    let value = guessValue(attribute, doc.rules[attribute])
                    if (!value.isRequired) {
                        //return
                    }

                    let attr = attribute
                    if (value.isArray) {
                        attr = attribute + "[]"
                    }
                    if (idx === 1) {
                        doc.url += "\n"+ "?"+attr+"="+value.value+"\n"
                    } else {
                        doc.url += "&"+attr+"="+value.value+"\n"
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
                    let value = guessValue(attribute, doc.rules[attribute])
                    if (value.isArray) {
                        body[attribute] = [value.value]
                    } else {
                        body[attribute] = value.value
                    }
                })
                doc.body = JSON.stringify(body, null, 2)
            }

        })
        Vue.use(VueMarkdown);

        var app = new Vue({
            el: '#app',
            data: {
                docs: docs,
                token: '',
                showRoute: false,
                requestHeaders: ''
            },
            created: function () {
                this.requestHeaders = 'X-CSRF-TOKEN:{{ csrf_token() }}'
                this.requestHeaders += '\n'
                this.requestHeaders += 'Accept:application/json'
                this.requestHeaders += '\n'
                this.requestHeaders += 'Authorization:Bearer ' + this.token
                this.requestHeaders += '\n'
            },
            methods: {
                highlightSidebar(idx) {
                    docs.map(function(doc, index) {
                        doc.isActiveSidebar = index == idx
                    })
                },
                highlighter(code) {
                    return Prism.highlight(code, Prism.languages.js, "js");
                },
                highlighterAtom(code) {
                    return Prism.highlight(code, Prism.languages.atom, "js");
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
                    doc.queries = []
                    doc.response = null
                    doc.responseOk = null
                    doc.responseTime = null
                    doc.loading = true

                    headers = this.requestHeaders.split("\n")

                    axios.defaults.headers.common['X-Request-LRD'] = 'lrd'

                    for (header of headers) {
                        let h = header.split(":")
                        let key = h[0]
                        let value = h[1]
                        if (key && value) {
                            axios.defaults.headers.common[key.trim()] = value.trim()
                        }
                    }

                    let startTime = new Date().getTime();
                    axios({
                        method: method,
                        url: url,
                        data: json,
                        decompress: true,
                        withCredentials: true
                      }).then(response => {
                        console.log(response)
                        doc.responseOk = true
                        if (response && response.data) {
                            if (response.data['_lrd']) {
                                doc.queries = response.data['_lrd']['queries']
                                delete response.data['_lrd']
                            }
                            doc.response = JSON.stringify(response.data, null, 2)
                            doc.responseCode = response.status
                        }

                      }).catch(error => {
                        doc.responseOk = false
                        console.log(error)
                        if (error && error.response && error.response.data) {
                            if (error.response.data['_lrd']) {
                                doc.queries = error.response.data['_lrd']['queries']
                                delete error.response.data['_lrd']
                            }
                            doc.responseCode = error.response.status;
                            doc.response = JSON.stringify(error.response.data, null, 2)
                        }

                      }).then(function () {
                        let endTime = new Date().getTime()
                        doc.loading = false
                        doc.responseTime = endTime - startTime;
                      })
                },
            },
          });
      </script>
   </body>
</html>
