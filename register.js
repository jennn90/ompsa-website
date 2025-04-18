import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-app.js";
import { getAuth, createUserWithEmailAndPassword, sendEmailVerification, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-auth.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-firestore.js";

// Firebase configuration
const firebaseConfig = {
  apiKey: "AIzaSyDFeHkyhAV9wUFToVbtoLQ_9-RiaiacJdY",
  authDomain: "login-43a6c.firebaseapp.com",
  projectId: "login-43a6c",
  storageBucket: "login-43a6c.firebasestorage.app",
  messagingSenderId: "777443964366",
  appId: "1:777443964366:web:ff0969c479535e40d8279f"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);

// Form submit listener
const registerForm = document.getElementById('registerForm');

registerForm.addEventListener('submit', (event) => {
    event.preventDefault(); // Stop page reload

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    createUserWithEmailAndPassword(auth, email, password)
        .then((userCredential) => {
            const user = userCredential.user;

            // Send email verification
            sendEmailVerification(user)
                .then(() => {
                    console.log("Verification email sent!");
                    alert("A verification email has been sent to your email address. Please check your inbox.");
                    
                    // Redirect user to login page after sign-up
                    window.location.href = "userinterface.html";  // Redirect to login page
                })
                .catch((error) => {
                    console.error("Error sending email verification: ", error);
                    alert("Error sending verification email: " + error.message);
                });
        })
        .catch((error) => {
            alert("Signup error: " + error.message);
        });
});

// Google Sign-In
  document.getElementById("google-login-btn").addEventListener("click", function () {
    const provider = new GoogleAuthProvider();
  
    signInWithPopup(auth, provider)
      .then((result) => {
        const user = result.user;
        alert("Logged in with Google as " + user.displayName);
        window.location.href = "userinterface.html";
      })
      .catch((error) => {
        alert("Google Sign-in Error: " + error.message);
        console.error(error);
      });
  });