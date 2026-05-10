// ══════════════════════════════════════
// DASHBOARD.JS  —  PawDiary
// ══════════════════════════════════════

// ── AUTH CHECK ──
const user = localStorage.getItem('pawdiary_user');
if (!user) window.location.href = 'index.html';

// ── WELCOME NAME ──
const welcomeEl = document.getElementById('welcomeUser');
if (welcomeEl) welcomeEl.textContent = user;

// ── LOGOUT ──
const logoutBtn = document.getElementById('logoutBtn');
if (logoutBtn) {
    logoutBtn.addEventListener('click', () => {
        localStorage.removeItem('pawdiary_user');
        window.location.href = 'index.html';
    });
}

// ── STORAGE KEYS ──
const PETS_KEY   = 'pawdiary_pets_'   + user;
const ACTS_KEY   = 'pawdiary_acts_'   + user;
const HEALTH_KEY = 'pawdiary_health_' + user;

const pets    = JSON.parse(localStorage.getItem(PETS_KEY)   || '[]');
const acts    = JSON.parse(localStorage.getItem(ACTS_KEY)   || '[]');
const health  = JSON.parse(localStorage.getItem(HEALTH_KEY) || '[]');

// ── SUMMARY COUNTS ──
document.getElementById('totalPets').textContent       = pets.length;
document.getElementById('totalActivities').textContent = acts.length;
document.getElementById('totalHealth').textContent     = health.length;

// Helper: Make table row
function makeRow(col1, col2, col3) {
    const row = document.createElement('div');
    row.className = 'table-row';
    [col1, col2, col3].forEach(val => {
        const span = document.createElement('span');
        if (val === null) {
            span.textContent = 'NULL';
            span.className   = 'null-val';
        } else {
            span.textContent = val;
        }
        row.appendChild(span);
    });
    return row;
}

// ═══════════════════════════════════
//  CHART 1 — Activities per Pet (Bar)
// ═══════════════════════════════════
(function buildBarChart() {
    const canvas   = document.getElementById('actByPetChart');
    const emptyMsg = document.getElementById('chartEmptyMsg');

    if (acts.length === 0 || pets.length === 0) {
        canvas.style.display   = 'none';
        emptyMsg.style.display = 'block';
        return;
    }

    const countMap = {};
    pets.forEach(p => countMap[p.name] = 0);
    acts.forEach(a => {
        if (a.petName in countMap) countMap[a.petName]++;
        else countMap[a.petName] = (countMap[a.petName] || 0) + 1;
    });

    const labels = Object.keys(countMap);
    const data   = Object.values(countMap);

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Activities',
                data,
                backgroundColor: '#e91e8c',
                borderRadius: 4,
                barPercentage: 0.5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f9f0f5' } }
            }
        }
    });
})();

// ═══════════════════════════════════
//  RECENT ACTIVITIES LIST
// ═══════════════════════════════════
(function buildRecentList() {
    const listContainer = document.getElementById('recentList');
    const emptyMsg = document.getElementById('recentEmptyMsg');
    const searchInput = document.getElementById('searchRecent');

    if (acts.length === 0) {
        emptyMsg.style.display = 'block';
        listContainer.style.display = 'none';
        searchInput.disabled = true;
        return;
    }

    emptyMsg.style.display = 'none';
    listContainer.style.display = 'block';

    // Header row
    const header = document.createElement('div');
    header.className = 'table-header';
    ['Pet', 'Activity', 'Date'].forEach(t => {
        const span = document.createElement('span');
        span.textContent = t;
        header.appendChild(span);
    });

    function renderList(filterText = '') {
        listContainer.innerHTML = '';
        listContainer.appendChild(header);

        // Sort acts by date descending (newest first)
        const sorted = [...acts].sort((a, b) => new Date(b.date) - new Date(a.date));

        let count = 0;
        sorted.forEach(a => {
            const match = a.petName.toLowerCase().includes(filterText) || a.type.toLowerCase().includes(filterText);
            if (match) {
                listContainer.appendChild(makeRow(a.petName, a.type, a.date));
                count++;
            }
        });

        if (count === 0) {
            const noRes = document.createElement('div');
            noRes.className = 'empty-state';
            noRes.style.padding = '15px';
            noRes.textContent = 'No matching activities found.';
            listContainer.appendChild(noRes);
        }
    }

    renderList();

    searchInput.addEventListener('input', (e) => {
        renderList(e.target.value.toLowerCase());
    });
})();

// ═══════════════════════════════════
//  SQL JOIN LOGIC
// ═══════════════════════════════════
let currentJoinType = 'inner';
let currentJoinData = [];

const joinTextMap = {
    inner: 'Shows activities <strong>only</strong> if a matching pet exists in both tables.',
    left: 'Shows <strong>all pets</strong>, including those without logged activities.',
    right: 'Shows <strong>all activities</strong>, even if the pet was deleted or missing.',
    outer: 'Shows <strong>everything</strong> from both tables, matched where possible.'
};

const joinColorMap = {
    inner: { bg: '#e0f0ff', color: '#1d4ed8' }, // Blue
    left:  { bg: '#d1fae5', color: '#047857' }, // Green
    right: { bg: '#fef3c7', color: '#b45309' }, // Orange
    outer: { bg: '#ede9fe', color: '#6d28d9' }  // Purple
};

function generateJoinData(type) {
    const data = [];
    
    if (type === 'inner') {
        pets.forEach(pet => {
            const petActs = acts.filter(a => a.petName === pet.name);
            petActs.forEach(a => data.push({ pet: pet.name, act: a.type, date: a.date }));
        });
    } 
    else if (type === 'left') {
        pets.forEach(pet => {
            const petActs = acts.filter(a => a.petName === pet.name);
            if (petActs.length === 0) data.push({ pet: pet.name, act: null, date: null });
            else petActs.forEach(a => data.push({ pet: pet.name, act: a.type, date: a.date }));
        });
    }
    else if (type === 'right') {
        acts.forEach(a => {
            const matchedPet = pets.find(p => p.name === a.petName);
            data.push({ pet: matchedPet ? matchedPet.name : null, act: a.type, date: a.date });
        });
    }
    else if (type === 'outer') {
        // Left side
        pets.forEach(pet => {
            const petActs = acts.filter(a => a.petName === pet.name);
            if (petActs.length === 0) data.push({ pet: pet.name, act: null, date: null });
            else petActs.forEach(a => data.push({ pet: pet.name, act: a.type, date: a.date }));
        });
        // Right side missing
        acts.forEach(a => {
            const matchedPet = pets.find(p => p.name === a.petName);
            if (!matchedPet) data.push({ pet: null, act: a.type, date: a.date });
        });
    }
    return data;
}

function renderJoinTable(filterText = '') {
    const container = document.getElementById('joinRows');
    const emptyMsg = document.getElementById('joinEmptyMsg');
    container.innerHTML = '';

    let count = 0;
    currentJoinData.forEach(row => {
        const petStr = row.pet ? row.pet.toLowerCase() : 'null';
        const actStr = row.act ? row.act.toLowerCase() : 'null';
        
        if (petStr.includes(filterText) || actStr.includes(filterText)) {
            container.appendChild(makeRow(row.pet, row.act, row.date));
            count++;
        }
    });

    if (count === 0) {
        emptyMsg.style.display = 'block';
        if (currentJoinData.length > 0) emptyMsg.textContent = 'No results found for your search.';
        else emptyMsg.textContent = 'No matching data. Add pets or activities first!';
    } else {
        emptyMsg.style.display = 'none';
    }
}

window.showJoin = function(type, btnElement) {
    // UI Update Tabs
    document.querySelectorAll('.join-tab').forEach(t => t.classList.remove('active'));
    btnElement.classList.add('active');

    // UI Update Badge & Text
    const badge = document.getElementById('joinBadge');
    badge.textContent = type.toUpperCase() + ' JOIN';
    badge.style.background = joinColorMap[type].bg;
    badge.style.color = joinColorMap[type].color;
    
    document.getElementById('joinText').innerHTML = joinTextMap[type];

    // Data Update
    currentJoinType = type;
    currentJoinData = generateJoinData(type);
    
    // Clear search
    document.getElementById('searchJoin').value = '';
    renderJoinTable();
};

// Search listener
document.getElementById('searchJoin').addEventListener('input', (e) => {
    renderJoinTable(e.target.value.toLowerCase());
});

// Initialize with INNER JOIN
currentJoinData = generateJoinData('inner');
renderJoinTable();