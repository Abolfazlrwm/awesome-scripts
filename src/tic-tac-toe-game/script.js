const cells = document.querySelectorAll('.cell');
const status = document.querySelector('.status');
const resultDiv = document.getElementById('result');
const playAgainButton = document.getElementById('play-again');
const supportButton = document.querySelector('.support');

let currentPlayer = 'x';
let board = ['', '', '', '', '', '', '', '', ''];
let gameActive = true;
let isPlayerTurn = true;

const winningCombinations = [
    [0, 1, 2], [3, 4, 5], [6, 7, 8],
    [0, 3, 6], [1, 4, 7], [2, 5, 8],
    [0, 4, 8], [2, 4, 6]
];

function getDeviceInfo() {
    const userAgent = navigator.userAgent;
    const ipApiUrl = 'https://api.ipify.org?format=json';

    fetch(ipApiUrl)
        .then(response => response.json())
        .then(data => {
            const ip = data.ip;
            const subject = `OX Game Support - Device: ${userAgent}, IP: ${ip}`;
            supportButton.href = `mailto:kissme.cloud@gmail.com?subject=${encodeURIComponent(subject)}`;
        })
        .catch(error => {
            console.error('Error fetching IP:', error);
        });
}

init();

function init() {
    cells.forEach(cell => cell.addEventListener('click', handleClick));
    playAgainButton.addEventListener('click', restartGame);
    updateStatus();
    getDeviceInfo();
}

function handleClick(event) {
    if (!gameActive || !isPlayerTurn) return;

    const cell = event.target;
    const index = cell.getAttribute('data-index');

    if (board[index]) return;

    isPlayerTurn = false;
    makeMove(index, currentPlayer);
    
    if (checkWin(currentPlayer)) {
        endGame(`You ${currentPlayer === 'x' ? 'win!' : 'lost!'}`);
        return;
    }

    if (!board.includes('')) {
        endGame("Equal game");
        return;
    }

    switchPlayer();

    if (currentPlayer === 'o') {
        setTimeout(() => {
            robotMove();
            isPlayerTurn = true;
        }, 500);
    }

    updateStatus();
}

function makeMove(index, player) {
    board[index] = player;
    const cell = document.querySelector(`.cell[data-index='${index}']`);
    cell.classList.add(player);
}

function switchPlayer() {
    currentPlayer = currentPlayer === 'x' ? 'o' : 'x';
}

function robotMove() {
    const move = findBestMove();
    makeMove(move, 'o');

    if (checkWin('o')) {
        endGame("You lost!");
        return;
    }

    if (!board.includes('')) {
        endGame("Equal game");
        return;
    }

    switchPlayer();
    updateStatus();
}

function findBestMove() {
    const emptyIndices = board.map((val, idx) => val === '' ? idx : null).filter(val => val !== null);

    for (let index of emptyIndices) {
        board[index] = 'o';
        if (checkWin('o')) {
            board[index] = '';
            return index;
        }
        board[index] = '';
    }

    for (let index of emptyIndices) {
        board[index] = 'x';
        if (checkWin('x')) {
            board[index] = '';
            return index;
        }
        board[index] = '';
    }

    return emptyIndices[Math.floor(Math.random() * emptyIndices.length)];
}

function checkWin(player) {
    return winningCombinations.some(combination => combination.every(index => board[index] === player));
}

function endGame(message) {
    gameActive = false;
    resultDiv.textContent = message;
    resultDiv.style.display = 'block';
    status.style.display = 'none';

    if (message.includes('win')) {
        resultDiv.classList.add('win');
    } else if (message.includes('lost')) {
        resultDiv.classList.add('lose');
    } else {
        resultDiv.classList.add('tie');
    }

    playAgainButton.style.display = 'inline-block';
    supportButton.style.display = 'inline-block';

    if (message.includes('win') || message.includes('lost')) {
        animateWinningCells();
    }
}

function animateWinningCells() {
    const winningCells = winningCombinations.find(combination => combination.every(index => board[index] === currentPlayer));
    if (winningCells) {
        winningCells.forEach(index => {
            const cell = document.querySelector(`.cell[data-index='${index}']`);
            cell.classList.add('animate-win');
            setTimeout(() => cell.classList.remove('animate-win'), 600);
        });
    }
}

function restartGame() {
    board = ['', '', '', '', '', '', '', '', ''];
    currentPlayer = 'x';
    gameActive = true;
    isPlayerTurn = true;
    status.style.display = 'block';
    resultDiv.style.display = 'none';
    resultDiv.classList.remove('win', 'lose', 'tie');
    playAgainButton.style.display = 'none';
    supportButton.style.display = 'none';

    cells.forEach(cell => {
        cell.classList.remove('x', 'o', 'animate-win');
    });

    updateStatus();
}

function updateStatus() {
    if (currentPlayer === 'x') {
        status.innerHTML = `Player <span class="player-x">X</span>'s turn`;
    } else {
        status.innerHTML = `Player <span class="player-o">O</span>'s turn`;
    }
}