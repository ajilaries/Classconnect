document.addEventListener("DOMContentLoaded", () => {
  const questionEl = document.getElementById("poll-question");
  const optionsEl = document.getElementById("poll-options");
  const form = document.getElementById("poll-form");
  const resultEl = document.getElementById("poll-result");

  let pollId = null;

  // Load poll from backend
  fetch("get_poll.php")
    .then(res => res.json())
    .then(data => {
      if (data.error) {
        questionEl.textContent = data.error;
        form.style.display = "none";
      } else {
        pollId = data.id;
        questionEl.textContent = data.question;
        data.options.forEach((opt, index) => {
          const label = document.createElement("label");
          label.className = "poll-option";
          label.innerHTML = `
            <input type="radio" name="option" value="${opt}"> ${opt}
          `;
          optionsEl.appendChild(label);
        });
      }
    });

  // Handle vote submit
  form.addEventListener("submit", e => {
    e.preventDefault();
    const selected = document.querySelector('input[name="option"]:checked');
    if (!selected) {
      alert("Please select an option.");
      return;
    }

    fetch("submit_vote.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `poll_id=${pollId}&option=${encodeURIComponent(selected.value)}`
    })
      .then(res => res.text())
      .then(msg => {
        resultEl.textContent = msg;
        form.style.display = "none";
      });
  });
});
