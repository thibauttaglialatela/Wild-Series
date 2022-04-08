/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css';

document.getElementById("watchlist").addEventListener('click', addToWatchlist);

function addToWatchlist(event) {
    event.preventDefault();

    let watchlistLink = event.currentTarget;
    let link = watchlistLink.href;
    //send an HTTP request with fetch to the URI defined in the href
    fetch(link)
        //Extract the JSON from the response
        .then(res => res.json())
        //Then update the icon
        .then(function (res) {
            let watchlistIcon = watchlistLink.firstElementChild;
            if (res.isInWatchlist) {
                watchlistIcon.classList.remove('bi-heart'); // Remove the .bi-heart (empty heart)
                watchlistIcon.classList.add('bi-heart-fill'); // Add the .bi-heart-fill
            } else {
                watchlistIcon.classList.remove('bi-heart-fill'); // Remove the bi-heart-fill
                watchlistIcon.classList.add('bi-heart');
            }
        });
}

