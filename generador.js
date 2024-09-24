let isSerie = document.getElementById("serie");
let isMovie = document.getElementById("movie");
let userInput = document.getElementById("movie-link");
let episodeContainer = document.getElementById("episodeContainer"); // Nuevo elemento
let types = document.querySelectorAll("input[type=radio][name=type]");
let numeroTemporadaInput = document.getElementById("numeroTemporada");
let inputsContainer = document.getElementById("inputs-container");
let api_key = "c29debe62758f3f450767c272e605067";
let language = "es-MX";

types.forEach((type) => {
    type.addEventListener("change", () => {
        if (type.value == "movie") {
            document.getElementById("season-selector").style.display = "none";
            userInput.placeholder = "Buscar película";

            // Ocultar el contenedor de episodios
            if (episodeContainer) {
                episodeContainer.style.display = 'none';
            }
        } else if (type.value == "serie") {
            document.getElementById("season-selector").style.display = "block";
            userInput.placeholder = "Buscar serie";

            // Mostrar el contenedor de episodios si estaba oculto previamente
            if (episodeContainer && episodeContainer.style.display === 'none') {
                episodeContainer.style.display = 'block';
            }
        }

        const searchTerm = searchInput.value;
        const searchType = isSerie.checked ? "tv" : "movie";
        if (searchTerm.trim() !== "") {
            searchMoviesOrSeries(searchTerm, searchType);
        }
    });
});


const searchInput = document.getElementById("movie-search");

searchInput.addEventListener("input", () => {
    const searchTerm = searchInput.value;
    const searchType = isSerie.checked ? "tv" : "movie";
    if (searchTerm.trim() !== "") {
        searchMoviesOrSeries(searchTerm, searchType);
    }
});


//movie
function updateInputVisibility() {
    userInput.style.display = isMovie.checked ? "block" : "none";
}

updateInputVisibility();
types.forEach((type) => {
    type.addEventListener("change", () => {
        updateInputVisibility();
        if (type.value === "movie") {
            // Si es una película, muestra el input
            userInput.style.display = "block";
        } else if (type.value === "serie") {
            // Si es una serie, oculta el input
            userInput.style.display = "none";
        }
    });
});

numeroTemporadaInput.addEventListener("input", () => {
    const serieId = document.getElementById("numero").value;
    generateInputsForAllSeasons(serieId);
});

async function searchMoviesOrSeries(searchTerm, searchType) {
    try {
        let url;
        if (searchType === "tv") {
            url = `https://api.themoviedb.org/3/search/tv?api_key=${api_key}&query=${searchTerm}&language=es-MX`;
        } else if (searchType === "movie") {
            url = `https://api.themoviedb.org/3/search/movie?api_key=${api_key}&query=${searchTerm}&language=es-MX`;
        } else {
            console.error("Tipo de búsqueda no válido");
            return;
        }

        const response = await fetch(url);

        if (response.status === 200) {
            const searchData = await response.json();
            displaySearchResults(searchData.results);
        } else {
            console.log("Error en la búsqueda");
        }
    } catch (error) {
        console.error(error);
    }
}

function toggleResults() {
    const searchResultsContainer = document.getElementById('search-results-container');
    const toggleResultsButton = document.getElementById('toggle-results');

    if (searchResultsContainer.style.display === 'none') {
        searchResultsContainer.style.display = 'flex';
        toggleResultsButton.innerText = 'Ocultar Resultados';
    } else {
        searchResultsContainer.style.display = 'none';
        toggleResultsButton.innerText = 'Mostrar Resultados';
    }
}

function displaySearchResults(results) {
    const resultsContainer = document.getElementById('search-results');
    const noResultsMessage = document.getElementById('no-results-message');
    const searchResultsContainer = document.getElementById('search-results-container');

    resultsContainer.innerHTML = ''; // Limpiar resultados anteriores

    if (results.length > 0) {
        results.forEach((result) => {
            const resultItem = document.createElement('div');
            resultItem.classList.add('search-result-item');

            // Verificar si la imagen está disponible
            const imageUrl = result.poster_path
                ? `https://image.tmdb.org/t/p/w300/${result.poster_path}`
                : 'https://www.puroverso.uy/images/virtuemart/product/9789974719019.jpg';

            resultItem.innerHTML = `
                 <img src="${imageUrl}" alt="${result.title || result.name}" />
                 <div class="result-info">
                     <p class="result-title"><strong>Título:</strong> <span>${result.title || result.name}</span></p>
                     <p class="result-year"><strong>Año:</strong> <span>${result.first_air_date ? result.first_air_date.slice(0, 4) : (result.release_date ? result.release_date.slice(0, 4) : 'N/A')}</span></p>
                     <p class="result-id"><strong>ID:</strong> <span>${result.id}</span></p>
                 </div>
             `;

            resultItem.addEventListener('click', () => {
                // Al hacer clic en un resultado, actualizar el campo ID y generar la información
                document.getElementById('numero').value = result.id;
                generateInputsForAllSeasons(result.id);
                document.getElementById("titulo-entrada").value = result.title || result.name;
                // Ocultar resultados después de hacer clic en un resultado
                searchResultsContainer.style.display = 'none';
                document.getElementById('toggle-results').innerText = 'Mostrar Resultados';
            });

            resultsContainer.appendChild(resultItem);
        });

        updateToggleResultsButton();

        // Mostrar resultados y ocultar mensaje de no hay resultados
        searchResultsContainer.style.display = 'block';
        noResultsMessage.style.display = 'none';
    } else {
        // Ocultar resultados y mostrar mensaje de no hay resultados
        searchResultsContainer.style.display = 'none';
        noResultsMessage.style.display = 'block';
    }
}


// Nueva función para actualizar el texto del botón cuando cambia la visibilidad del contenedor
function updateToggleResultsButton() {
    const searchResultsContainer = document.getElementById('search-results-container');
    const toggleResultsButton = document.getElementById('toggle-results');

    if (searchResultsContainer.style.display === 'none') {
        toggleResultsButton.innerText = 'Mostrar Resultados';
    } else {
        toggleResultsButton.innerText = 'Ocultar Resultados';
    }
}

function convertMinutes(minutess) {
    let hours = Math.floor(minutess / 60),
        minutes = Math.floor(minutess % 60),
        total = "";

    if (minutess < 60) {
        total = `${minutes}m`;
        return total;
    } else if (minutess > 60) {
        total = `${hours}h ${minutes}m`;
        return total;
    } else if ((minutess === 60)) {
        total = `${hours}h`;
        return total;
    }
}

function formatLink(originalLink) {
    // Verificar si el enlace original ya está en el formato esperado
    if (originalLink.includes("streamwish.to/e/")) {
        return originalLink; // Devolver el enlace original si ya está en el formato esperado
    }

    // Extraer el código del enlace original
    const match = originalLink.match(/[^\/]+$/);
    if (match) {
        const code = match[0];
        // Construir el enlace en el formato esperado
        return `https://streamwish.to/e/${code}`;
    } else {
        // Devolver el enlace original si no se puede extraer el código
        return originalLink;
    }
}

// Inicializar el array bidimensional para almacenar los enlaces de cada temporada
let episodeContent = [];

async function generateInputsForAllSeasons(serieId) {
    try {
        // Limpiar el contenedor de inputs antes de agregar nuevos
        let inputsContainer = document.getElementById("inputs-container");
        inputsContainer.innerHTML = "";

        // Obtener el número total de temporadas de la serie
        let seriesResponse = await fetch(
            `https://api.themoviedb.org/3/tv/${serieId}?api_key=c29debe62758f3f450767c272e605067&language=es-MX`
        );

        if (seriesResponse.status === 200) {
            let serieData = await seriesResponse.json();

            // Iterar sobre cada temporada
            for (let seasonNumber = 1; seasonNumber <= serieData.number_of_seasons; seasonNumber++) {
                // Obtener datos de la temporada actual
                let seasonDataResponse = await fetch(
                    `https://api.themoviedb.org/3/tv/${serieId}/season/${seasonNumber}?api_key=c29debe62758f3f450767c272e605067&language=es-MX`
                );

                if (seasonDataResponse.status === 200) {
                    let seasonData = await seasonDataResponse.json();
                    let episodeCount = seasonData.episodes.length;

                    // Crear un contenedor para los inputs de la temporada actual
                    let seasonContainer = document.createElement("div");
                    seasonContainer.classList.add("season-container");
                    seasonContainer.id = `season-${seasonNumber}`;

                    // Crear un título para la temporada
                    let seasonTitle = document.createElement("h2");
                    seasonTitle.textContent = `Temporada ${seasonNumber}`;
                    seasonContainer.appendChild(seasonTitle);

                    // Crear un array para almacenar los enlaces de la temporada actual
                    let seasonLinks = [];

                    // Generar inputs para cada episodio de la temporada actual
                    for (let i = 0; i < episodeCount; i++) {
                        let inputId = `episode-${seasonNumber}-${i + 1}`;

                        // Crear el input
                        let input = document.createElement("input");
                        input.type = "text";
                        input.id = inputId;
                        input.placeholder = `Episodio ${i + 1}`;
                        // Agregar un event listener para escuchar cambios en el input
                        input.addEventListener("input", (event) => {
                            let inputValue = event.target.value;
                            // Guardar el último cambio en el array
                            seasonLinks[i] = inputValue;
                            console.log(episodeContent);
                        });
                        seasonContainer.appendChild(input);
                    }

                    // Agregar el contenedor de la temporada al contenedor principal
                    inputsContainer.appendChild(seasonContainer);
                    // Agregar el array de enlaces de la temporada al array bidimensional
                    episodeContent.push(seasonLinks);
                } else {
                    console.log(`Error al obtener información de la temporada ${seasonNumber}`);
                }
            }
            console.log(episodeContent); // Aquí puedes ver el contenido de episodeContent
            return episodeContent; // Devolver episodeContent
        } else {
            console.log("Error al obtener información de la serie");
        }
    } catch (error) {
        console.error("Error inesperado:", error);
    }
}



// Array global para almacenar los géneros
let generosGlobal = [];

async function generar() {
    let serieKey = document.getElementById("numero").value;

    const cargarPeliculas = async () => {
        if (isSerie.checked) {
            try {
                const serieId = document.getElementById("numero").value;
                const serieResponse = await fetch(`https://api.themoviedb.org/3/tv/${serieId}?api_key=${api_key}&language=${language}`);
                const serieData = await serieResponse.json();

                // Obtener los creadores de la serie
                let creators = '';
                serieData.created_by.forEach((creator, index) => {
                    creators += creator.name;
                    if (index < serieData.created_by.length - 1) {
                        creators += ', ';
                    }
                });

                // Obtener los géneros de la serie
                let tags = '';
                serieData.genres.forEach((genre, index) => {
                    // Agregar una coma si no es el primer género
                    if (index !== 0) {
                        tags += ', ';
                    }
                    // Reemplazar "&" por ","
                    const genreName = genre.name.replace(' & ', ', ');
                    tags += genreName;
                });

                let episodesHTML = '';
                const episodeContent = await generateInputsForAllSeasons(serieId); // Obtener los enlaces de cada episodio

                for (let seasonNumber = 1; seasonNumber <= serieData.number_of_seasons; seasonNumber++) {
                    const seasonResponse = await fetch(`https://api.themoviedb.org/3/tv/${serieId}/season/${seasonNumber}?api_key=${api_key}&language=${language}`);
                    const seasonData = await seasonResponse.json();

                    const episodes = [];
                    for (const [index, episode] of seasonData.episodes.entries()) {
                        // Obtener el enlace del episodio correspondiente
                        const episodeUrl = episodeContent[seasonNumber - 1][index]; // Los índices en los arrays comienzan en 0, por eso restamos 1 a seasonNumber

                        // Obtener la sinopsis del episodio
                        let synopsis = episode.overview;

                        // Si la sinopsis no está disponible en es-MX, intentar obtenerla en es
                        if (!synopsis) {
                            const esResponse = await fetch(`https://api.themoviedb.org/3/tv/${serieId}/season/${seasonNumber}/episode/${episode.episode_number}/translations?api_key=${api_key}`);
                            const esData = await esResponse.json();
                            const esOverview = esData.translations.find(trans => trans.iso_3166_1 === 'ES');
                            synopsis = esOverview ? esOverview.data.overview : 'No disponible en este idioma';
                        }

                        // Construir el HTML para cada episodio
                        const imageUrl = `https://image.tmdb.org/t/p/w300/${episode.still_path}`;
                        const runtime = episode.runtime ? `${episode.runtime}m` : 'N/A';
                        const episodeHTML = `
                            <li>
                                <a href="#!" class="episode episode-link" data-url="${episodeUrl}">
                                    <div class="episode__img">
                                        <img src="${imageUrl}" onerror="this.style='display:none';">
                                        <div class="episode__no-image"><i class="fa-regular fa-circle-play"></i></div>
                                    </div>
                                    <div class="episode__info">
                                        <h4 class="episode__info__title">${episode.episode_number}. ${episode.name}</h4>
                                        <div class="episode__info__duration">${runtime}</div>
                                        <p class="sinopsis-info">${synopsis}</p>
                                    </div>
                                </a>
                            </li>`;
                        episodes.push(episodeHTML);
                    }

                    // Determinar la clase para el contenedor de episodios
                    const seasonClass = seasonNumber === 1 ? 'animation' : 'hide';

                    // Agregar los episodios de la temporada actual al HTML final
                    episodesHTML += `
                        <ul class="caps-grid ${seasonClass}" id="season-${seasonNumber}">
                            ${episodes.join('')}
                        </ul>`;
                }


                let seasonsOption = "";

                serieData.seasons.forEach(season => {
                    if (season.name != "Especiales") {
                        seasonsOption += `<option value="${season.season_number}">Temporada ${season.season_number}</option>
                    `;
                    }
                });

                let genSeasonsCount;

                if (serieData.number_of_seasons == 1) {
                    genSeasonsCount = " Temporada";
                } else if (serieData.number_of_seasons > 1) {
                    genSeasonsCount = " Temporadas";
                }

                // Construir el HTML final
                let htmlFinal = `
                <div class="post-header">
                <div class="filter">
                <div class="background-image" style="background-image: url(https://image.tmdb.org/t/p/w500${serieData.backdrop_path});"></div>
                        </div>
                <div class="image-and-btn">
                <img src="https://image.tmdb.org/t/p/w300/${serieData.poster_path}" class="poster-img" alt="" />
                <div class="fav-js">
                <button class="bs-favs" card-id="${serieData.id}" id="add-btn"><i class="fa-regular fa-heart"></i> Añadir a mi lista</button>
                <button class="delete-btn none-btn" card-id="${serieData.id}" id="remove-btn"><i class="fa-solid fa-trash"></i> Borrar de mi lista</button>
                </div>
                </div>
                
                <div class="post-header__info">
                <h1>${serieData.name}</h1>
                <ul>
                <li class="tmdb-rate"><i class="fa-solid fa-star"></i> ${serieData.vote_average.toFixed(1)}</li>
                <li>${serieData.number_of_seasons + genSeasonsCount}</li>
                <li>${serieData.first_air_date.slice(0, 4)}</li>
                </ul>
                <p class="resume">${serieData.overview}</p>
                <div class="more-data">
                <p>Género: ${tags}</p>
                <p>Created by: ${creators}</p>
                </div>
                </div>
                </div>
                <!-- Ventana Modal Video -->
                <div id="myModal" class="modal-videos">
                <div class="modal-content-videos" id="transmitirEpisode">
                <span id="closeModal" onclick="closeModal">&times;</span>
                <div id="modalContent" class="iframe-container"></div>
                </div>
                </div>
                <!--more-->
                <div class="season-list">
                <div class="select-season">
                <h2>Episodios</h2>
                <select name="" id="select-season">
                ${seasonsOption}
                </select>
                </div>
                <div id="temps">
                ${episodesHTML}
                </div>
                </div>`;

                // Agregar el HTML final al elemento con el ID "html-final"
                document.getElementById('html-final').innerText = htmlFinal;

                // Limpiar el contenido del array episodeContent
                episodeContent.length = 0;
            } catch (error) {
                console.error('Error al generar los episodios:', error);
            }


        } else if (isMovie.checked) {
            try {
                const respuesta = await fetch(
                    `https://api.themoviedb.org/3/movie/${serieKey}?api_key=c29debe62758f3f450767c272e605067&language=${language}`
                );

                if (respuesta.status === 200) {
                    const datos = await respuesta.json();
                    // console.log(datos);//

                    let ageRatingHtml = ""; // Variable para almacenar el HTML de la clasificación por edad

                    const idiomasPredeterminados = ["AR", "ES", "EN", "MX", "UA", "BR", "PR", "CO", "CL", "PE", "VE", "EC", "GT", "CU", "BO", "DO", "HN", "PY", "SV", "NI", "CR", "PA", "UY", "JM", "BS", "BB", "HT", "TT", "KY", "BM", "GY", "SR", "BZ", "AN", "LC", "AG", "DM", "GD", "KN", "VC", "TT", "US", "DE", "FR", "GB"]; // Agrega aquí los idiomas que desees buscar

                    const certificationResponse = await fetch(
                        `https://api.themoviedb.org/3/movie/${serieKey}/release_dates?api_key=c29debe62758f3f450767c272e605067`
                    );

                    if (certificationResponse.status === 200) {
                        const certifications = await certificationResponse.json();

                        for (const idioma of idiomasPredeterminados) {
                            const certification = certifications.results.find((result) => result.iso_3166_1 === idioma);

                            if (certification && certification.release_dates.length > 0) {
                                const ageRating = certification.release_dates[0].certification;

                                if (ageRating && !isNaN(ageRating.trim())) {
                                    console.log(`Clasificación por edad para ${idioma}: ${ageRating}`);
                                    // Puedes utilizar ageRating en tu HTML o en cualquier otra parte de tu código.
                                    ageRatingHtml = `<p class="section__date-edad">+${ageRating}</p>`;
                                    break; // Salimos del bucle una vez que encontramos una clasificación por edad válida.
                                } else {
                                    console.log(`La clasificación por edad para ${idioma} no es un número válido.`);
                                    // Manejar la situación en la que la clasificación por edad no es un número válido.
                                }
                            } else {
                                console.log(`No se encontró clasificación por edad para ${idioma}`);
                                // Manejar la situación en la que no se encuentra la clasificación por edad para el idioma especificado.
                            }
                        }
                    } else {
                        console.log("Error al obtener la clasificación por edad");
                        // Manejar el error al obtener la clasificación por edad.
                    }

                    // Obtén el valor del primer input
                    const userInputA = document.getElementById('userInputA').value;
                    const userInputB = document.getElementById('userInputB').value;
                    // Función para convertir el enlace al formato deseado
                    function convertirFormato(codigo) {
                        // Extrae el código después de la última barra "/"
                        const codigoDespuesDeBarra = codigo.substring(codigo.lastIndexOf('/') + 1);

                        // Formato 1: oneupload.to/embed-{código}
                        const formatoUno = `streamwish.to/e/${codigoDespuesDeBarra}`;

                        // Formato 2: https://oneupload.to/embed-{código}
                        const formatoDos = `https://${formatoUno}`;

                        return { formatoUno, formatoDos };
                    }

                    // Aplica la conversión al valor del primer input
                    const resultado = convertirFormato(userInputA);

                    // Ahora 'resultado' contiene los enlaces convertidos
                    console.log(resultado.formatoUno); // Muestra: oneupload.to/embed-7s56g3blv0s6
                    console.log(resultado.formatoDos); // Muestra: https://oneupload.to/embed-7s56g3blv0s6


                    // Obtener los géneros de la serie
                    let tags = '';
                    datos.genres.forEach((genre, index) => {
                        // Agregar una coma si no es el primer género
                        if (index !== 0) {
                            tags += ', ';
                        }
                        // Reemplazar "&" por ","
                        const genreName = genre.name.replace(' & ', ', ');
                        tags += genreName;
                    });

                    let template = document.getElementById("html-final");

                    let justHtml = `
                    <!--${tags}-->
                    <!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Post</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    /* Loader Styles */
    #loader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: black;
      z-index: 1000;
    }

    .spinner {
    border: 5px solid rgba(255, 255, 255, 0.3); /* Borde externo con transparencia */
    border-top: 5px solid #00FF4B; /* Cambia el color aquí a verde */
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
}

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Main Content Styles */
    .hidden {
      display: none;
    }

    .post-header {
      display: flex;
      align-items: flex-start;
      padding: 10px;
    }

    .poster-container {
      flex-shrink: 0;
      margin-right: 15px;
    }

    .poster-img {
      width: 150px;
      height: auto;
      border-radius: 5px;
    }

    .post-details {
      flex: 1;
    }

    .post-details h2 {
      font-size: 0.8em;
      margin-top: 0;
      font-weight: bold;
      margin-left: -15px; /* Ajusta este valor según lo que desees */
      white-space: nowrap; /* Evita que el texto salte a la siguiente línea */
      overflow: hidden; /* Oculta el desbordamiento del texto */
      text-overflow: ellipsis; /* Añade '...' si el texto es muy largo */
    }

    .post-details ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .post-details ul li {
      margin-bottom: 5px;
      display: flex;
      align-items: center;
      gap: 5px;
      font-weight: bold;
    }

    .post-details ul li i {
      font-size: 1.2em;
    }

    .fav-js {
      margin-top: 10px;
    }

    .resume {
      margin-top: 8px;
      padding: 10px;
      border-radius: 10px;
      font-weight: bold;
      background-color: #121212; /* Background color for the resume */
    }

    .more-data {
      margin-top: 10px;
    }

    .hacker {
      margin-top: 5px;
      padding: 25px;
      border: none;
      background-color: #15BA42;
      color: #fff;
      border-radius: 5px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      font-size: 1em;
      text-align: center;
      font-weight: bold;
    }

    .hacker i {
      font-size: 1.2em;
    }

    .hacker:hover {
      background-color: #9e0505ff;
    }
  </style>
</head>
<body>
  <!-- Loader -->
  <div id="loader">
    <div class="spinner"></div>
  </div>

  <!-- Main Content -->
  <div id="content" class="hidden">
    <div class="post-header">
      <div class="poster-container">
        <img src="https://image.tmdb.org/t/p/w300${datos.poster_path}" class="poster-img" alt="Póster" />
      </div>
      <div class="post-details">
        <ul>
          <li><i class=""></i></li><br>
          <li><h2>${datos.title}</h2></li>
          <br>
          <li><i class="fa-solid fa-calendar"></i>${datos.release_date.slice(0, 4)}</li><br>
          <li><i class="fa-solid fa-clock"></i>${convertMinutes(datos.runtime)}</li><br>
          <li><i class="fas fa-star" style="color: yellow;"></i> ${datos.vote_average.toFixed(1)}</li>
        </ul>
        <div class="more-data">
        </div>
        <div class="fav-js">
        </div>
      </div>
    </div>
    <a href="http://pelistart.free.nf/embed/play.php?url=${userInputA}&img=https://image.tmdb.org/t/p/w500${datos.backdrop_path}" class="hacker">
      <i class="fa-solid fa-play fa-beat"></i><b>Reproducir</b>
    </a>
    <a href="wvc-x-callback://open?url=http://pelistart.free.nf/embed/descarga.php?url=${userInputA}" class="hacker">
    <i class="fa-brands fa-chromecast fa-beat"></i><b>Transmitir</b>
</a>
    <p class="resume">${datos.overview}</p>
  </div>

  <script>
    window.addEventListener('load', function() {
      setTimeout(function() {
        document.getElementById('loader').style.display = 'none';
        document.getElementById('content').classList.remove('hidden');
      }, 1000); // Espera 1000 milisegundos (1 segundo)
    });
  </script>
</body>
</html>

                    `;
                    template.innerText = justHtml;

                    let genPoster = document.getElementById("info-poster");
                    let genTitle = document.getElementById("info-title");
                    let genSeasons = document.getElementById("info-seasons");
                    let genYear = document.getElementById("info-year");
                    let genSinopsis = document.getElementById("info-sinopsis")

                    genPoster.setAttribute(
                        "src",
                        `https://image.tmdb.org/t/p/w300/${datos.poster_path}`
                    );
                    genTitle.innerText = datos.title;
                    genSeasons.innerText = "";
                    genYear.innerText = datos.release_date.slice(0, 4);
                    genSinopsis.innerText = datos.overview;
                } else if (respuesta.status === 401) {
                    console.log("Wrong key");
                } else if (respuesta.status === 404) {
                    console.log("No existe");
                }
            } catch (error) {
                console.log(error);
            }
        }

    };

    await cargarPeliculas();
}


document.getElementById('copiar').addEventListener('click', function () {
    // Seleccionar el texto dentro del elemento html-final
    const htmlFinalText = document.getElementById('html-final').innerText;

    // Crear un elemento textarea temporal para copiar el texto
    const tempTextArea = document.createElement('textarea');
    tempTextArea.value = htmlFinalText;

    // Agregar el elemento textarea al cuerpo del documento
    document.body.appendChild(tempTextArea);

    // Seleccionar y copiar el texto dentro del textarea temporal
    tempTextArea.select();
    document.execCommand('copy');

    // Remover el textarea temporal
    document.body.removeChild(tempTextArea);

    // Notificar al usuario que el texto ha sido copiado
    alert('El HTML ha sido copiado al portapapeles.');
});

//aqui

// Función para iniciar sesión y obtener el token de acceso
function iniciarSesion() {
    window.location.href = 'https://accounts.google.com/o/oauth2/auth?' +
        'client_id=392804236703-llkr2saq5eao69qj10kd3or2dav3eg70.apps.googleusercontent.com' +
        '&redirect_uri=https://firego.a0001.net' +
        '&response_type=token' +
        '&scope=https://www.googleapis.com/auth/blogger' +
        '&approval_prompt=force';
}

// Función para obtener el token de acceso de la URL
function obtenerTokenDeURL() {
    var tokenRegex = /access_token=([^&]+)/;
    var match = window.location.hash.match(tokenRegex);
    if (match && match[1]) {
        var token = match[1];
        var tiempoExpiracion = Date.now() + 3500000; // Calcular el tiempo de expiración del token (3500 segundos en milisegundos)
        // Guardar el token de acceso y el tiempo de expiración en el almacenamiento local
        localStorage.setItem('accessToken', token);
        localStorage.setItem('tiempoExpiracion', tiempoExpiracion);
        // Insertar el token de acceso en el input
        document.getElementById('accessTokenInput').value = token;
        // Iniciar el contador de tiempo restante solo si no se ha iniciado previamente
        if (!localStorage.getItem('contadorIniciado')) {
            iniciarContador();
            localStorage.setItem('contadorIniciado', 'true');
        }
    }
}

// Función para iniciar el contador de tiempo restante
function iniciarContador() {
    // Obtener el tiempo de expiración del token del almacenamiento local
    var tiempoExpiracion = localStorage.getItem('tiempoExpiracion');
    // Calcular el tiempo restante en milisegundos
    var tiempoRestante = tiempoExpiracion - Date.now();

    // Actualizar el contador en el DOM
    mostrarTiempoRestante(tiempoRestante);

    // Actualizar el contador cada segundo
    var contadorInterval = setInterval(function () {
        // Calcular el nuevo tiempo restante
        tiempoRestante = tiempoExpiracion - Date.now();
        // Verificar si el tiempo restante ha alcanzado cero
        if (tiempoRestante <= 0) {
            // Limpiar el intervalo y eliminar el token de acceso y el tiempo de expiración del almacenamiento local
            clearInterval(contadorInterval);
            localStorage.removeItem('accessToken');
            localStorage.removeItem('tiempoExpiracion');
            localStorage.removeItem('contadorIniciado');
            // Actualizar el contador en el DOM
            mostrarTiempoRestante(0);
        } else {
            // Actualizar el contador en el DOM
            mostrarTiempoRestante(tiempoRestante);
        }
    }, 1000);
}

function mostrarTiempoRestante(tiempoRestante) {
    var segundosRestantes = Math.ceil(tiempoRestante / 1000);
    var contadorElemento = document.getElementById('contador');
    contadorElemento.textContent = 'Tiempo restante: ' + segundosRestantes + ' segundos';
    console.log('Mostrando tiempo restante:', segundosRestantes);
}

// Llamar a la función para obtener el token de acceso de la URL cuando se cargue la página
obtenerTokenDeURL();

// Función para enviar el HTML a Blogger
function enviarHTML() {
    const tituloEntrada = document.getElementById('titulo-entrada').value;
    var token = localStorage.getItem('accessToken');
    if (token) {
        var html = document.getElementById('html-final').innerText;

        // Obtener los géneros del array global y formatearlos como etiquetas
        const etiquetas = generosGlobal.join(', ');

        // Lógica para enviar el HTML a Blogger utilizando el token de acceso
        fetch('https://www.googleapis.com/blogger/v3/blogs/7419122148924124873/posts/', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                'kind': 'blogger#post',
                'blog': {
                    'id': '7419122148924124873'
                },
                'title': tituloEntrada,
                'content': html
            })
        })
            .then(response => {
                if (response.ok) {
                    alert('HTML enviado a Blogger exitosamente.');
                    // Limpiar el array de géneros después de enviar el HTML
                    generosGlobal = [];
                } else {
                    alert('Error al enviar HTML a Blogger: ' + response.statusText);
                }
            })
            .catch(error => alert('Error de red: ' + error));
    } else {
        alert('No se ha encontrado un token de acceso.');
    }
}