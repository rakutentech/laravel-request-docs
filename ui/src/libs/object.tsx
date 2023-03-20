export const objectToFormData = (obj: any, formData?: FormData, namespace?: string): FormData => {
    formData = formData || new FormData();
    for (const property in obj) {
      if (obj[property] === undefined) {
        continue;
      }

      const formKey = namespace ? `${namespace}[${property}]` : property;

      if (obj[property] instanceof FileList) {
        for (let i = 0; i < obj[property].length; i++) {
          formData.append(`${formKey}[${i}]`, obj[property][i]);
        }
      } else if (obj[property] instanceof File) {
        formData.append(formKey, obj[property]);
      } else if (typeof obj[property] === 'object') {
        if (obj[property] instanceof Date) {
          formData.append(formKey, obj[property].toISOString());
        } else {
          objectToFormData(obj[property], formData, formKey);
        }
      } else {
        formData.append(formKey, obj[property].toString());
      }
    }
    return formData;
}
