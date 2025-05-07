function toggleDropdown() {
    const dropdown = document.getElementById('dropdown');
    dropdown.classList.toggle('hidden');
}

function selectMood(moodId) {
    const content = document.getElementById('content').value.trim();
    if (!content) {
        alert("Please write something before selecting your mood.");
        return;
    }

    document.getElementById('mood_id').value = moodId;
    document.getElementById('entryForm').submit();
}
