import createCrudModule from 'vuex-crud';
import {client, parseResponse} from '@/api'

export default createCrudModule({
  resource: 'clients',
  urlRoot: 'api/clients',
  idAttribute: 'slug',
  client,
  parseSingle: parseResponse,
  parseList: parseResponse
});
