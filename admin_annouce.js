document.addEventListener("DOMContentLoaded", function() {
    const postBtn = document.querySelector("button");
    const textarea = document.querySelector("textarea");
    const fileInput = document.getElementById("fileInput");
    const postsContainer = document.getElementById("postsContainer");
    const calendarIcon = document.getElementById("calendarIcon");
    const calendar = document.getElementById("calendar");
  
    let calendarMargin = 20;
  
    postBtn.addEventListener("click", function() {
        const announcementText = textarea.value.trim();
        const file = fileInput.files[0];
  
        if (announcementText === "" && !file) {
            alert("Please write an announcement or select an image file.");
            return;
        }
  
        const newPost = document.createElement("div");
        newPost.className = "post-entry";
  
        // Add the announcement text if there is any
        if (announcementText !== "") {
            const textPara = document.createElement("p");
            textPara.textContent = announcementText;
            newPost.appendChild(textPara);
        }
  
        // Add the image if there is one
        if (file && file.type.startsWith("image/")) {
            const img = document.createElement("img");
            img.src = URL.createObjectURL(file);
            img.alt = "Announcement Image";
            newPost.appendChild(img);
        }
  
        // Create the delete button
        const deleteBtn = document.createElement("button");
        deleteBtn.textContent = "Delete";
        deleteBtn.classList.add("delete-btn");
        newPost.appendChild(deleteBtn);
  
        // Add the delete functionality
        deleteBtn.addEventListener("click", function() {
            newPost.remove();
        });
  
        // Prepend the new post to the container
        postsContainer.prepend(newPost);
  
        // Clear the input fields
        textarea.value = "";
        fileInput.value = "";
  
        // Update calendar margin
        calendarIcon.style.transform = "rotate(10deg)";
        calendarMargin += 10;
        calendar.style.marginTop = calendarMargin + "px";
        setTimeout(() => {
            calendarIcon.style.transform = "rotate(0deg)";
        }, 300);
    });
  });
  