

// Function to add a card
function addCard(sectionId) {
  const container = document.getElementById(sectionId);
  if (!container) {
    alert(`No container found with ID: ${sectionId}`);
    return;
  }

  const card = document.createElement('div');
  card.className = 'card';
  card.innerHTML = `
    <img src="https://via.placeholder.com/100" onclick="this.nextElementSibling.click()" alt="New Person">
    <input type="file" accept="image/*" onchange="changePicture(this)">
    <div class="name" contenteditable="true">New Name</div>
    <div class="position" contenteditable="true">Position</div>
    <div class="btn-container">
      <button class="edit-btn" onclick="toggleEdit(this)">Save</button>
      <button class="delete-btn" onclick="deleteCard(this)">Delete</button>
    </div>
  `;
  container.appendChild(card);
}

// Function to toggle edit mode
function toggleEdit(button) {
  const card = button.closest('.card');
  const nameEl = card.querySelector('.name');
  const positionEl = card.querySelector('.position');
  const editing = nameEl.isContentEditable;

  nameEl.contentEditable = !editing;
  positionEl.contentEditable = !editing;

  button.textContent = editing ? 'Edit' : 'Save';
}

// Function to delete a card
function deleteCard(button) {
  const card = button.closest('.card');
  card.remove();
}


// ✅ Make functions accessible globally (for inline `onclick`)
window.addCard = addCard;
window.toggleEdit = toggleEdit;
window.deleteCard = deleteCard;





