import createCrudModule from 'vuex-crud';
import {client, parseResponse} from '../../client.js'

export default createCrudModule({
  resource: 'attachments',
  urlRoot: '../attachment/attachments',
  client,
  parseSingle: parseResponse,
  parseList: parseResponse
});
