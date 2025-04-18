import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-app.js";
import { getAuth, 
  signInWithEmailAndPassword, 
  sendPasswordResetEmail,
  sendEmailVerification, 
  GoogleAuthProvider, 
  signInWithPopup } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-auth.js";

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

// Email/Password Login
document.getElementById("loginForm").addEventListener("submit", function(event) {
  event.preventDefault();

  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();
  const emailError = document.getElementById("emailError");
  const passwordError = document.getElementById("passwordError");

  // Clear previous errors
  emailError.style.display = "none";
  passwordError.style.display = "none";

  if (email === "") {
    emailError.textContent = "Email is required.";
    emailError.style.display = "block";
    return;
  }

  if (password === "") {
    passwordError.textContent = "Password is required.";
    passwordError.style.display = "block";
    return;
  }

  signInWithEmailAndPassword(auth, email, password)
    .then((userCredential) => {
      const user = userCredential.user;

      if (!user.emailVerified) {
        alert("Please verify your email before logging in.");
        sendEmailVerification(user)
          .then(() => {
            alert("Verification email resent. Please check your inbox.");
          })
          .catch((error) => {
            alert("Error resending verification email: " + error.message);
          });

        return;
      }

      alert("Login successful!");

      if (document.getElementById("remember-me").checked) {
        localStorage.setItem('email', email);
        localStorage.setItem('password', password);
        localStorage.setItem('rememberMe', 'true');
      } else {
        localStorage.removeItem('email');
        localStorage.removeItem('password');
        localStorage.setItem('rememberMe', 'false');
      }

      window.location.href = "userinterface.html";
    })
    .catch((error) => {
      if (error.code === "auth/user-not-found") {
        emailError.textContent = "No account found with this email.";
        emailError.style.display = "block";
      } else if (error.code === "auth/wrong-password") {
        passwordError.textContent = "Incorrect password. Please try again.";
        passwordError.style.display = "block";
      } else {
        alert("Login error: " + error.message);
      }
    });
});

// Forgot Password
document.querySelector(".forgot-password").addEventListener("click", function(event) {
    event.preventDefault();
  
    const email = prompt("Enter your email to reset password:");
  
    if (email) {
      sendPasswordResetEmail(auth, email)
        .then(() => {
          alert("Password reset email sent! Please check your inbox.");
        })
        .catch((error) => {
          let message = "Error: " + error.message;
          if (error.code === "auth/user-not-found") {
            message = "No account found with this email.";
          }
          alert(message);
        });
    } else {
      alert("Please enter a valid email address.");
    }
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
