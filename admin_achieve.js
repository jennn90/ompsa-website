const isAdmin = true;

    function renderAchievements() {
      const container = document.getElementById("achievementsGrid");
      container.innerHTML = "";

      achievements.forEach((item, index) => {
        const card = document.createElement("div");
        card.className = "card";

        card.innerHTML = `
          <img src="${item.image}" alt="${item.title}">
          <h3 contenteditable="${isAdmin}" class="editable-title">${item.title}</h3>
          <p contenteditable="${isAdmin}" class="editable-desc">${item.description}</p>
          <button class="edit-btn" onclick="saveAchievement(${index})">Save</button>
          <button class="delete-btn" onclick="deleteAchievement(${index})">Delete</button>
        `;

        container.appendChild(card);
      });
    }

    function saveAchievement(index) {
      const card = document.querySelectorAll('.card')[index];
      const newTitle = card.querySelector('.editable-title').innerText;
      const newDesc = card.querySelector('.editable-desc').innerText;

      achievements[index].title = newTitle;
      achievements[index].description = newDesc;

      alert("Achievement updated!");
    }

    function deleteAchievement(index) {
      if (confirm("Are you sure you want to delete this achievement?")) {
        achievements.splice(index, 1);
        renderAchievements();
      }
    }

    function addAchievement() {
      const title = document.getElementById("newTitle").value.trim();
      const imageFile = document.getElementById("newImage").files[0];
      const description = document.getElementById("newDescription").value.trim();

      if (!title || !imageFile || !description) {
        alert("Please fill in all fields.");
        return;
      }

      const reader = new FileReader();
      reader.onload = function (event) {
        const imageURL = event.target.result;

        achievements.push({
          title: title,
          image: imageURL,
          description: description
        });

        renderAchievements();

        // Clear form fields
        document.getElementById("newTitle").value = "";
        document.getElementById("newImage").value = "";
        document.getElementById("newDescription").value = "";
      };

      reader.readAsDataURL(imageFile);
    }

    renderAchievements();