window.selectedApplicants = new Set();
document.addEventListener("DOMContentLoaded", function () {
    const typeSelect = document.getElementById("type");
    const rspExtension = document.getElementById("rsp-extension");
    const tableBody = document.getElementById("applicant-table-body");
    const searchInput = document.getElementById("search-applicant");
    const filterBy = document.getElementById("filter-by");


    fetchApplicants();

    // Show/hide extension
    typeSelect.addEventListener("change", function () {
        if (typeSelect.options[typeSelect.selectedIndex].text === "RSP Interview") {
            rspExtension.style.display = "block";
            fetchApplicants();
        } else {
            rspExtension.style.display = "none";
            tableBody.innerHTML = "";
        }
    });

    // Fetch applicants from server
    function fetchApplicants() {
        fetch("util/fetch_applicant.php")
            .then(async res => {
                const text = await res.text();
                try {
                    const data = JSON.parse(text);
                    window.allApplicants = data;
                    renderTable(data);
                } catch (err) {
                    console.error("Error parsing JSON:", err);
                    console.error("Raw response:", text);
                }
            })
            .catch(err => {
                console.error("Error fetching applicants:", err);
            });
    }

    // Render the table and preserve checked state
    function renderTable(applicants) {
        tableBody.innerHTML = applicants.map(app => `
            <tr>
                <td>
                    <input type="checkbox" name="applicant_select[]" value="${app.id}" 
                        ${selectedApplicants.has(app.id.toString()) ? "checked" : ""}>
                </td>
                <td>${app.name}</td>
                <td>${app.department}</td>
                <td>${app.position}</td>
            </tr>
        `).join('');

        // Add event listeners to update selectedApplicants set
        document.querySelectorAll('input[name="applicant_select[]"]').forEach(cb => {
            cb.addEventListener('change', () => {
                if (cb.checked) {
                    selectedApplicants.add(cb.value);
                } else {
                    selectedApplicants.delete(cb.value);
                }
            });
        });
    }

    // Search and filter logic
    function applySearch() {
        const query = searchInput.value.toLowerCase();
        let results = window.allApplicants;

        if (query) {
            results = results.filter(app =>
                app.name?.toLowerCase().includes(query)
            );
        }

        results = applySort(results);
        renderTable(results);
    }

    function applySort(data) {
        const sortBy = filterBy.value;

        if (sortBy === "all") return data;
        return [...data].sort((a, b) =>
            (a[sortBy] || "").localeCompare(b[sortBy] || "")
        );
    }

    searchInput.addEventListener("input", applySearch);
    filterBy.addEventListener("change", applySearch);
});
