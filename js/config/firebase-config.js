import { initializeApp } from "https://www.gstatic.com/firebasejs/10.10.0/firebase-app.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/10.10.0/firebase-firestore.js";

// Your web app's Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyAyLlHjkJkSDp2Nufz52_0h2VfU6vV2mOs",
    authDomain: "web-example-f9662.firebaseapp.com",
    projectId: "web-example-f9662",
    storageBucket: "web-example-f9662.firebasestorage.app",
    messagingSenderId: "219613686367",
    appId: "1:219613686367:web:d443213db52f4e540e09ea",
    measurementId: "G-NNM6SFYE26"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
export const db = getFirestore(app);