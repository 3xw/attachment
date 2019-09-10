import axios from 'axios'

const
Http = axios.create({
  headers: {
    //'X-CSRF-TOKEN'    : window.csrfToken,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

class client extends Http {}

const
parseResponse = function(response)
{
  return {
    data: response.data.data// expecting object with ID
  }
}

export { client, parseResponse }
