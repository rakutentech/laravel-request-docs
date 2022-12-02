interface APIInfo {
  uri: string;
  methods: string[];
  middlewares: string[];
  controller: string;
  controller_full_path: string;
  method: string;
  httpMethod: string;
  rules: APIRule;
  docBlock: string;
}

interface APIRule {
  [key: string]: string[];
}