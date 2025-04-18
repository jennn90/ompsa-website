import { auth } from './firebase-config.js';
import { GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/9.6.1/firebase-auth.js";

const provider = new GoogleAuthProvider();

const handleLogin = () => {
  signInWithPopup(auth, provider)
    .then(result => result.user.getIdToken())
    .then(token => {
      return fetch('session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ idToken: token, role: 'admin' })
      });
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        window.location.href = 'admin-dashboard.php';
      } else {
        alert('Unauthorized admin');
      }
    })
    .catch(error => {
      console.error('Login error:', error);
      alert('Login failed');
    });
};

document.getElementById('adminLoginBtn').addEventListener('click', handleLogin);



