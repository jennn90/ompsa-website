import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-app.js";
import { getAuth, onAuthStateChanged, signOut, updateProfile } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-auth.js";
import { getStorage, ref, uploadBytes, getDownloadURL } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-storage.js";
import { getFirestore, doc, setDoc } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-firestore.js";

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
const storage = getStorage(app);
const db = getFirestore(app);

// Elements
const profileBtn = document.getElementById("profileBtn");
const profilePanel = document.getElementById("profilePanel");
const profilePic = document.getElementById("profilePic");
const profilePicInput = document.getElementById("profilePicInput");
const usernameInput = document.getElementById("usernameInput");
const saveChangesBtn = document.getElementById("saveChangesBtn");
const logoutBtn = document.getElementById("logoutBtn");
const usernameDisplay = document.getElementById("username");

// Toggle dropdown panel
profileBtn.addEventListener("click", () => {
  profilePanel.classList.toggle("show");
});

// Outside click closes the panel
window.addEventListener("click", (e) => {
  if (!profileBtn.contains(e.target) && !profilePanel.contains(e.target)) {
    profilePanel.classList.remove("show");
  }
});

// 🔄 Preview selected image
profilePicInput.addEventListener("change", () => {
  const file = profilePicInput.files[0];
  if (file && file.type.startsWith("image/")) {
    const reader = new FileReader();
    reader.onload = (e) => {
      profilePic.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
});

// Load user info
onAuthStateChanged(auth, (user) => {
  if (user) {
    usernameDisplay.textContent = user.displayName || "User";
    profilePic.src = user.photoURL || "profile.jpg";
    usernameInput.value = user.displayName || "";
  } else {
    window.location.href = "login.html";
  }
});

// Save changes
saveChangesBtn.addEventListener("click", () => {
    const user = auth.currentUser;
    const newName = usernameInput.value;
    const file = profilePicInput.files[0];
  
    if (!user) return;
  
    if (file) {
      const storageRef = ref(storage, `profile_pics/${user.uid}`);
      uploadBytes(storageRef, file).then(snapshot => {
        return getDownloadURL(snapshot.ref);
      }).then(downloadURL => {
        // Update user profile with display name and photo URL in Firebase Authentication
        return updateProfile(user, {
          displayName: newName,
          photoURL: downloadURL
        });
      }).then(() => {
        // Save the user data to Firestore (storing the image URL, not the image itself)
        saveUserDataToFirestore(user, newName, downloadURL);
        alert("Profile updated!");
        window.location.reload();
      }).catch(console.error);
    } else {
      updateProfile(user, {
        displayName: newName
      }).then(() => {
        saveUserDataToFirestore(user, newName, user.photoURL);
        alert("Name updated!");
        window.location.reload();
      }).catch(console.error);
    }
  });
  
  // Save User Data to Firestore
  const saveUserDataToFirestore = (user, newName, imageURL) => {
    const userRef = doc(db, "users", user.uid);
    setDoc(userRef, {
      displayName: newName,
      photoURL: imageURL  // Storing the URL, not the actual image.
    })
    .then(() => {
      console.log("User data saved!");
    })
    .catch((error) => {
      console.error("Error saving user data: ", error);
    });
  };

// Logout
logoutBtn.addEventListener("click", () => {
  signOut(auth).then(() => {
    window.location.href = "login.html";
  }).catch(console.error);
});
