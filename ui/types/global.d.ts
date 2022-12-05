interface IAPIInfo {
  uri: string;
  methods: string[];
  middlewares: string[];
  controller: string;
  controller_full_path: string;
  method: string;
  httpMethod: string;
  rules: IAPIRule;
  docBlock: string;
}

interface IAPIRule {
  [key: string]: string[];
}