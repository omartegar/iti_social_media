// All frontend requests use this deployed backend.
const API_BASE_URL = "https://iti-api.wasmer.app:443";

// Authentication
// signup expects FormData with user fields and an optional image.
export const signup = `${API_BASE_URL}/signup`;

// login expects JSON: { email, password }.
export const login = `${API_BASE_URL}/login`;

// Home and posts
// These endpoints require: Authorization: localStorage.getItem("token").
export const getHomeData = `${API_BASE_URL}/getHomeData`;

// createPost expects FormData with text, image, or both.
export const createPost = `${API_BASE_URL}/createPost`;

// deletePost expects JSON: { id }.
export const deletePost = `${API_BASE_URL}/deletePost`;

// Users
export const myInfo = `${API_BASE_URL}/myInfo`;
export const showAllUsers = `${API_BASE_URL}/showAllUsers`;

// Messages
// getPrivateMessages expects the receiver id as a query string: ?id=123.
export const getPrivateMessages = `${API_BASE_URL}/getPrivateMessages`;

// sendPrivateMessage expects JSON: { receiver_id, message }.
export const sendPrivateMessage = `${API_BASE_URL}/sendPrivateMessage`;

// METHOD: POST
// addLikeToPost needs : post_id key in body json.stringify({})
export const createLike = `${API_BASE_URL}/createLike`;
// but with method: DELETE
export const deleteLike = `${API_BASE_URL}/createLike`;
