// Made by Omar Ahmed Hashem
// API's
const serverIp = "https://iti-api.wasmer.app";
const port = "443";

// signup API: method => POST
// body: {firstname, lastname, phone, email, image, password, confirmpassword}
export const signup = `${serverIp}:${port}/signup`;

// login API: method => POST
// body: {email, password}
export const login = `${serverIp}:${port}/login`;

// getHomeData API: method => GET
// headers: {Authorization: localStorage.getItem('token')}
export const getHomeData = `${serverIp}:${port}/getHomeData`;

// createPost API: method => POST
// headers: {Authorization: localStorage.getItem('token')}
// body: {text, image}   ,, One or both
export const createPost = `${serverIp}:${port}/createPost`;

// deletePost API: method => DELETE
// headers: {Authorization: localStorage.getItem('token')}
// body: JSON.stringify({id})
export const deletePost = `${serverIp}:${port}/deletePost`;

// getUserProfile API: method => GET
// headers: {Authorization: localStorage.getItem('token')}
export const myInfo = `${serverIp}:${port}/myInfo`;

/*
fetch() usage:

fetch("API_URL_ENDPOINT", OPTIONAL_OPTIONS);

example 1:

async function myFunc()
{

    const response = fetch("API_URL_ENDPOINT", {
        method:"GET",
        headers: {},
        body: {}
    });

    const data = await response.json();

    if(data.status === "success"){
        // BACKEND WORKS AND SENT ANSWER
    }else{
        // BACKEND SAYS THERE IS AN ERROR
        // CHECK IT USING message
        console.log(data.message);  
    }
}



*/
