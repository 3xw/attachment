import createCrudModule from 'vuex-crud';
import {client, parseResponse} from '../../client.js'

export default createCrudModule({
  resource: 'atags',
  urlRoot: '../attachment/atags',
  client,
  parseSingle: parseResponse,
  parseList: parseResponse
});
