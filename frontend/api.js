// Made by Omar Ahmed Hashem
// API's
const serverIp = "https://iti-api.wasmer.app";
const port = "443";

// signup API: method => POST
// body: {firstname, lastname, phone,email,image,password,confirmpassword}
export const signup = `${serverIp}:${port}/signup`;

// login API: method => POST
// body: {email, password}
export const login = `${serverIp}:${port}/login`;

// getHomeData API: method => GET
// headers: {Authorization: localStorage.getItem('token')}
export const getHomeData = `${serverIp}:${port}/getHomeData`;
