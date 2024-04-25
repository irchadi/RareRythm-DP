document.getElementById('sort').addEventListener('change', function() {
    fetch('sort_music.php?sort=' + this.value)
    .then(response => response.json())
    .then(morceaux => {
        const container = document.getElementById('music-list');
        container.innerHTML = ''; // Vider le conteneur
        morceaux.forEach(morceau => {
            // Créer la carte pour chaque morceau
            const card = `<div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">${morceau.titre}</h5>
                    <p class="card-text">${morceau.description}</p>
                    <audio controls>
                        <source src="musique/${morceau.fichier_audio}" type="audio/mpeg">
                        Votre navigateur ne supporte pas l'élément audio.
                    </audio>
                </div>
            </div>`;
            container.innerHTML += card; // Ajouter la carte au conteneur
        });
    });
});

document.querySelectorAll('.genre-badge').forEach(badge => {
  badge.addEventListener('click', function() {
    const genre = this.getAttribute('data-genre');
    document.querySelectorAll('.card').forEach(card => {
      const cardGenre = card.querySelector('.genre-badge').getAttribute('data-genre');
      if (cardGenre === genre || genre === 'Tous') {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });
  });
});

document.getElementById('reset-sort').addEventListener('click', function() {
  window.location.href = 'musique.php'; // ou l'URL actuelle sans paramètres
});

// Reconstruire les cartes après un tri
morceaux.forEach(morceau => {
  const card = `
      <div class="card mb-3">
          <div class="card-body">
              <h5 class="card-title">${morceau.titre}</h5>
              <p class="card-text">${morceau.description}</p>
              <span class="badge bg-secondary genre-badge">${morceau.genre_nom}</span>
              <audio controls>
                  <source src="musique/${morceau.fichier_audio}" type="audio/mpeg">
                  Votre navigateur ne supporte pas l'élément audio.
              </audio>
          </div>
      </div>
  `;
  container.innerHTML += card; // Ajouter la carte au conteneur
});

document.getElementById('sort').addEventListener('change', function() {
  fetch(`sort_music.php?sort=${this.value}`)
  .then(response => response.json())
  .then(morceaux => {
      const container = document.getElementById('music-list');
      container.innerHTML = ''; // Vider le conteneur
      morceaux.forEach(morceau => {
          // Créer la carte pour chaque morceau en utilisant les données JSON
          const card = `
              <div class="card mb-3">
                  <div class="card-body">
                      <h5 class="card-title">${morceau.titre}</h5>
                      <p class="card-text">${morceau.description}</p>
                      <span class="badge bg-secondary">${morceau.genre_nom}</span>
                      <audio controls>
                          <source src="musique/${morceau.fichier_audio}" type="audio/mpeg">
                          Votre navigateur ne supporte pas l'élément audio.
                      </audio>
                  </div>
              </div>
          `;
          container.innerHTML += card; // Ajouter la carte au conteneur
      });
  })
  .catch(error => console.error('Error:', error));
});