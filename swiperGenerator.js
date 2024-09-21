let isSerie = document.getElementById('serie');
let isMovie = document.getElementById('movie');

let types = document.querySelectorAll('input[type=radio][name=type]');




function convertMinutes(minutess){
    let hours = Math.floor(minutess / 60) ,
    minutes = Math.floor(minutess % 60),
    total = '';

    if (minutess < 60){
        total = `${minutes}m`
        return total
    } else if (minutess > 60){
      total = `${hours}h ${minutes}m`
      return total
    } else if (minutess = 60){
        total = `${hours}h`
        return total
    }
}

function generar() {
    let serieKey = document.getElementById('numero').value;
    let languaje = "es-MX"

    const cargarPeliculas = async() => {

        if (isSerie.checked) {
            try {

                const respuesta = await fetch(`https://api.themoviedb.org/3/tv/${serieKey}?api_key=c29debe62758f3f450767c272e605067&language=${languaje}`);
    
                if (respuesta.status === 200) {
                    const datos = await respuesta.json();
                    console.log(datos)
                        
                    let tags = '';
    
                    datos.genres.forEach(genre => {
                        if (genre.name != datos.genres[datos.genres.length - 1].name) {
                            tags += `${genre.name}, `
                        } else {
                            tags += datos.genres[datos.genres.length - 1].name
                        }
                    });

    
                    let genSeasonsCount;
    
                    if (datos.number_of_seasons == 1){
                        genSeasonsCount = " Temporada"
                    } else if (datos.number_of_seasons > 1){
                        genSeasonsCount = " Temporadas"
                    }
                    
                    let template = document.getElementById('html-final');
    
                    let justHtml = ` 
<!-- ${datos.name} -->
<div class="swiper-slide">
    <div class="bs-card">
        <div class="card-cover" style="background: url('https://image.tmdb.org/t/p/w780/${datos.backdrop_path}') no-repeat center 20% / cover;" />
        <div class="card-content">
            <h1 class="card-title">${datos.name}</h1>
            <p class="card-tags">${datos.number_of_seasons + genSeasonsCount}</p>
            <p class="card-resume">
            ${datos.overview}
            </p>
            <div>
                <a class="btn btn-blur" href="#"><i class="fa-solid fa-play" /> Ver ahora</a>
            </div>
        </div>
    </div>
</div>

`;
                    
                    
    
                    const btnCopiar = document.getElementById('copiar');
    
                    
                    template.innerText = justHtml;
                   
    
                    let templateHTML = template.innerText;
                    btnCopiar.addEventListener('click', () => {
                        navigator.clipboard.writeText(justHtml);
                    })

                    
                    let genPoster = document.getElementById('info-poster');
                    let genTitle = document.getElementById('info-title');
                    let genSeasons = document.getElementById('info-seasons');
                    let genYear = document.getElementById('info-year');
    
                    genPoster.setAttribute('src', `https://image.tmdb.org/t/p/w500/${datos.poster_path}`)
                    genTitle.innerText = datos.name;
                    genSeasons.innerText = datos.number_of_seasons + genSeasonsCount;
                    genYear.innerText = datos.first_air_date.slice(0,4);
    
    
    
                } else if (respuesta.status === 401) {
                    console.log('Wrong key');
                } else if (respuesta.status === 404) {
                    console.log('No existe');
                }
    
            } catch (error) {
                console.log(error);
            }
        } else
        if(isMovie.checked){
            try {

            const respuesta = await fetch(`https://api.themoviedb.org/3/movie/${serieKey}?api_key=c29debe62758f3f450767c272e605067&language=${languaje}`);

            if (respuesta.status === 200) {
                const datos = await respuesta.json();
                let tags = '';

                datos.genres.forEach(genre => {
                    if (genre.name != datos.genres[datos.genres.length - 1].name) {
                        tags += `${genre.name}, `
                    } else {
                        tags += datos.genres[datos.genres.length - 1].name
                    }
                });

               
            
                    let template = document.getElementById('html-final');

                    let justHtml = `
<!-- ${datos.title} -->
<div class="swiper-slide">
    <div class="bs-card">
        <div class="card-cover" style="background: url('https://image.tmdb.org/t/p/w780/${datos.backdrop_path}') no-repeat center 20% / cover;" />
        <div class="card-content">
            <h1 class="card-title">${datos.title}</h1>
            <p class="card-tags">${convertMinutes(datos.runtime)} &#x2022; ${datos.release_date.slice(0,4)}</p>
            <p class="card-resume">
            ${datos.overview}
            </p>
            <div>
                <a class="btn btn-blur" href="#"><i class="fa-solid fa-play" /> Ver ahora</a>
            </div>
        </div>
    </div>
</div>

`;                  
                    template.innerText = justHtml;
                    
                    const btnCopiar = document.getElementById('copiar');
                    
                    btnCopiar.addEventListener('click', () => {
                        navigator.clipboard.writeText(justHtml);
                    })
    
    
                    let genPoster = document.getElementById('info-poster');
                    let genTitle = document.getElementById('info-title');
                    let genSeasons = document.getElementById('info-seasons');
                    let genYear = document.getElementById('info-year');
    
                    genPoster.setAttribute('src', `https://image.tmdb.org/t/p/w500/${datos.poster_path}`)
                    genTitle.innerText = datos.title;
                    genSeasons.innerText = "";
                    genYear.innerText = datos.release_date.slice(0,4);
    
    
    
                } else if (respuesta.status === 401) {
                    console.log('Wrong key');
                } else if (respuesta.status === 404) {
                    console.log('No existe');
                }
    
            } catch (error) {
                console.log(error);
            }           
        }

    }

    cargarPeliculas();
}

generar();



