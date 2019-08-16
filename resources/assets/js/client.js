import axios from 'axios'

const
Http = axios.create({
  baseURL: process.env.PUBLIC_PATH,
  headers: {
    //'X-CSRF-TOKEN'    : window.csrfToken,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

class client extends Http {}

const
//client = new Client(), // so client is same instance
parseResponse = function(response)
{
  return {
    data: response.data.data// expecting object with ID
  }
}

export { client, parseResponse }
