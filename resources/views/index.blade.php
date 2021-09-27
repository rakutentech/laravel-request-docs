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
      <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.4/axios.min.js"></script>
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
            <div class="container px-4 mx-auto md:flex md:items-center">
                <div class="flex justify-between items-center">
                    <a href="{{config('request-docs.url')}}" class="font-bold text-xl text-indigo-600">{{ config('request-docs.document_name') }}</a>
                </div>
            </div>
        </nav>
      <div id="app" v-cloak class="w-full flex lg:pt-10">
         <aside class="text-xl text-grey-darkest break-all bg-gray-200 pl-2 h-screen sticky top-1 overflow-auto" style="width: 35%">
            <h1 class="font-light mx-3">Routes List</h1>
             <div class="dropdown">
                 <input
                     v-model.trim="search"
                     class="dropdown-input appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                     type="text"
                     placeholder="Search"
                     @focus="showRoute = true;$event.target.select()"
                     @blur="setTimeout(() => { showRoute = false}, 200)"
                 />
                 <div class="dropdown-list">
                     @foreach ($docs as $index => $doc)
                     <div  v-if="filter('{{ $doc["uri"] }}')"
                        v-show="showRoute"
                        @click="searched('{{ $doc["methods"][0] }}', '{{ $doc["uri"] }}') ; highlightSidebar('{{ $index }}')"
                        class="dropdown-item">
                         {{ str_replace('api/', '', $doc['uri']) }}
                     </div>
                     @endforeach
                 </div>
             </div>
            <hr class="border-b border-gray-300">
            <table class="table-fixed text-sm mt-5">
                <tbody>
                    @php
                        $previousController = ['controller' => null];
                    @endphp
                    @foreach ($docs as $index => $doc)
                    <tr>
                        <td>
                            @if ($previousController['controller'] !== $doc['controller'])
                                <h3 class="mt-2 font-thin">{{ str_replace('Controller', '', $doc['controller']) }}</h3>
                            @endif
                            <a href="#{{$doc['methods'][0] .'-'. $doc['uri']}}" @click="highlightSidebar({{$index}})">
                                <span class="
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
                                    text-{{in_array('POST', $doc['methods']) ? 'black': ''}} bg-{{in_array('POST', $doc['methods']) ? 'red': ''}}-500
                                    text-{{in_array('PUT', $doc['methods']) ? 'black': ''}}-100 bg-{{in_array('PUT', $doc['methods']) ? 'yellow': ''}}-500
                                    text-{{in_array('PATCH', $doc['methods']) ? 'black': ''}}-100 bg-{{in_array('PATCH', $doc['methods']) ? 'yellow': ''}}-500
                                    text-{{in_array('DELETE', $doc['methods']) ? 'white': ''}} bg-{{in_array('DELETE', $doc['methods']) ? 'black': ''}}
                                    ">
                                    {{$doc['methods'][0]}}
                                </span>
                                <span class="text-xs" v-bind:class="docs[{{$index}}]['isActiveSidebar'] ? 'font-bold':''">
                                    <span class="font-bold text-green-600 border rounded-full pr-1 pl-1 border-green-500" v-if="docs[{{$index}}]['responseOk'] === true">✓</span>
                                    <span class="font-bold text-red-600 border rounded-full pr-1 pl-1 border-red-500" v-if="docs[{{$index}}]['responseOk'] === false">✗</span>
                                    {{$doc['uri']}}
                                </span>
                            </a>
                        </td>
                    </tr>
                    @php
                        $previousController = $doc;
                    @endphp
                    @endforeach
                </tbody>
            </table>
        </aside>
         <br><br>
         <div class="ml-6 mr-6 pl-2 w-2/3 bg-gray-300 p-2" style="width: 100%">
            @foreach ($docs as $index => $doc)
            <section class="pt-5 pl-2 pr-2 pb-5 border mb-10 rounded bg-white shadow">
                <div class="font-sans" id="{{$doc['methods'][0] .'-'. $doc['uri']}}">
                <h1 class="text-sm break-normal text-black bg-indigo-50 break-normal font-sans pb-1 pt-1 text-black">
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
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-gray-700 bg-yellow-400 rounded">Array</span>
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
                    class="bg-red-500 hover:bg-red-700 text-white font-bold mt-2 border-red-800 border-2 shadow-inner mb-1 pl-5 pr-5 rounded-full"
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
                            <h3 class="font-thin">REQUEST URL<sup class="text-red-500 font-bold"> *required</sup></h3>
                            <p class="text-xs pb-2 font-thin text-gray-500">Enter your request URL with query params</p>
                            <prism-editor class="my-prism-editor"
                            style="padding:15px 0px 15px 0px;min-height:20px;background:#2d2d2d;color: #ccc;resize:both;"
                            v-model="docs[{{$index}}]['url']" :highlight="highlighter" line-numbers></prism-editor>
                            <br>
                            @if (!in_array('GET', $doc['methods']))
                            <h3 class="font-thin">REQUEST BODY<sup class="text-red-500"> *required</sup></h3>
                            <p class="text-xs pb-2 font-thin text-gray-500">JSON body for the POST|PUT|DELETE request</p>
                            <prism-editor class="my-prism-editor" style="min-height:200px;background:#2d2d2d;color: #ccc;resize:both" v-model="docs[{{$index}}]['body']" :highlight="highlighter" line-numbers></prism-editor>
                            @endif
                        </div>
                    </div>
                    <div class="">
                        <div v-if="docs[{{$index}}]['response'] && !docs[{{$index}}]['cancel']">
                            <hr class="border-b border-dotted mt-4 mb-2 border-gray-300">
                            <h3 class="font-thin">
                                RESPONSE
                                <span
                                    v-if="docs[{{$index}}]['responseOk']"
                                    class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-green-100 bg-green-500 rounded">
                                    OK
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
                            </h3>
                            <p class="text-xs pb-2 font-thin text-gray-500">Response from the server</p>
                            <prism-editor v-if="docs[{{$index}}]['response']" class="my-prism-editor shadow-inner border-gray-400 border-2 rounded" style="min-height:200px;max-height:700px;background:rgb(241 241 241);color: rgb(48 36 36);resize:both;" readonly v-model="docs[{{$index}}]['response']" :highlight="highlighterAtom" line-numbers></prism-editor>
                            <div class="mt-2">
                                <h3 class="font-thin">
                                    SQL
                                </h3>
                                <p class="text-xs pb-2 font-thin text-gray-500">SQL query log executed for this request</p>
                                <div v-for="query in docs[{{$index}}]['queries']" class="mb-2">
                                    <p class="text-sm font-thin">Query took: <span class="">@{{query.time}}ms</span></p>
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
        Vue.use(VueMarkdown);

        var app = new Vue({
            el: '#app',
            data: {
                docs: docs,
                token: '',
                search: '',
                showRoute: false
            },
            methods: {
                filter(uri) {
                    if (!this.search) {
                        return true
                    }
                    var similarity = stringSimilarity.compareTwoStrings(uri, this.search);
                    return similarity > 0.1
                },
                searched(method, uri) {
                    var oldUrl = new URL(document.URL);
                    oldUrl.hash = '#' + method + '-' + uri;
                    var newUrl = oldUrl.href;
                    document.location.href = newUrl;
                    this.search = uri.replace('api/', '');
                },
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
                    doc.loading = true
                    axios.defaults.headers.common['X-Request-LRD'] = 'lrd'
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}'

                    if (doc.bearer) {
                        axios.defaults.headers.common = {
                            Authorization: `Bearer ${this.token}`,
                            Accept: `application/json`,
                            'X-Request-LRD': 'lrd',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        };
                    }

                    axios({
                        method: method,
                        url: url,
                        data: json,
                        withCredentials: true
                      }).then((response) => {
                        if (response['_lrd']) {
                            doc.queries = response['_lrd']['queries']
                            delete response['_lrd']
                        }
                        // in case of validation error
                        if (response.data && response.data && response.data['_lrd']) {
                            doc.queries = response.data['_lrd']['queries']
                            delete response.data['_lrd']
                        }

                        // in case of validation error
                        if (response.data && response.data.data && response.data.data['_lrd']) {
                            doc.queries = response.data.data['_lrd']['queries']
                            delete response.data.data['_lrd']
                        }
                        doc.response = JSON.stringify(response.data, null, 2)
                        doc.responseCode = response.status
                        doc.responseOk = true
                      }).catch((error) => {
                        if (error['_lrd']) {
                            // split array to new lines
                            doc.queries = error['_lrd']['queries']
                            delete error['_lrd']
                        }
                        if (error.data && error.data['_lrd']) {
                            doc.queries = error.data['_lrd']['queries']
                            delete error.data['_lrd']
                        }
                        doc.loading = false
                        if (error && error.response && error.response.data) {
                            doc.response = JSON.stringify(error.response.data, null, 2)
                            doc.responseCode = error.response.status;
                        }

                        doc.responseOk = false
                      }).then(function () {
                        doc.loading = false
                      })
                },
            },
          });
      </script>
   </body>
</html>
