/**
 * DataTables Auto-Search Module
 * Enables instant search and filtering across all columns
 * Supports text inputs and dropdown filters
 */

class DataTableAutoSearch {
    constructor(tableSelector, options = {}) {
        this.tableSelector = tableSelector;
        this.options = {
            columns: [],
            dropdownFilters: {},
            textSearchDelay: 300,
            ...options
        };
        this.table = null;
        this.searchTimeout = null;
        this.init();
    }

    /**
     * Initialize DataTable and attach event listeners
     */
    init() {
        // Initialize DataTable
        this.table = $(this.tableSelector).DataTable({
            responsive: true,
            pageLength: 25,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            paging: true,
            dom: '<"top"lf>rt<"bottom"ip>',
            language: {
                search: "Search:",
                searchPlaceholder: "Type to search...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries available",
                zeroRecords: "No matching records found"
            }
        });

        // Attach event listeners
        this.attachSearchListeners();
        this.attachDropdownListeners();
    }

    /**
     * Attach listeners to text input search boxes
     */
    attachSearchListeners() {
        // Main DataTable search box
        const dataTableSearchInput = $(this.tableSelector + '_filter input');
        if (dataTableSearchInput.length) {
            dataTableSearchInput.on('keyup', () => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.table.search(dataTableSearchInput.val()).draw();
                }, this.options.textSearchDelay);
            });

            // Also allow Enter key to trigger immediate search
            dataTableSearchInput.on('keypress', (e) => {
                if (e.which === 13) { // Enter key
                    clearTimeout(this.searchTimeout);
                    this.table.search(dataTableSearchInput.val()).draw();
                }
            });
        }

        // Additional custom search inputs
        Object.keys(this.options.dropdownFilters).forEach(filterName => {
            const searchInput = $(`#${filterName}-search`);
            if (searchInput.length && this.options.dropdownFilters[filterName].type === 'text') {
                searchInput.on('keyup', () => {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => {
                        this.applyAllFilters();
                    }, this.options.textSearchDelay);
                });
            }
        });
    }

    /**
     * Attach listeners to dropdown filters
     */
    attachDropdownListeners() {
        Object.keys(this.options.dropdownFilters).forEach(filterName => {
            const filterElement = $(`#${filterName}`);
            if (filterElement.length && this.options.dropdownFilters[filterName].type === 'select') {
                filterElement.on('change', () => {
                    this.applyAllFilters();
                });
            }
        });
    }

    /**
     * Apply all active filters
     */
    applyAllFilters() {
        if ($.fn.dataTable.isDataTable(this.tableSelector)) {
            this.table.draw();
        }
    }

    /**
     * Add a custom filter function (for advanced filtering)
     * Usage: addCustomFilter((rowNode, data) => { return shouldShow; })
     */
    addCustomFilter(filterFunction) {
        $.fn.dataTable.ext.search.push(filterFunction);
        this.applyAllFilters();
    }

    /**
     * Clear all filters
     */
    clearAllFilters() {
        Object.keys(this.options.dropdownFilters).forEach(filterName => {
            $(`#${filterName}`).val('').trigger('change');
        });
        
        const dataTableSearchInput = $(this.tableSelector + '_filter input');
        if (dataTableSearchInput.length) {
            dataTableSearchInput.val('').trigger('keyup');
        }
        
        this.table.search('').draw();
    }

    /**
     * Get current filter values
     */
    getCurrentFilters() {
        const filters = {};
        Object.keys(this.options.dropdownFilters).forEach(filterName => {
            filters[filterName] = $(`#${filterName}`).val();
        });
        return filters;
    }
}

/**
 * Utility function to initialize a DataTable with specific column-based filtering
 */
function initializeColumnFilter(tableSelector, columnIndex, filterSelector) {
    const table = $(tableSelector).DataTable();
    
    $(filterSelector).on('change keyup', function() {
        table.column(columnIndex).search(this.value).draw();
    });
}

/**
 * Initialize multiple column filters
 * Usage: initializeMultipleColumnFilters(tableSelector, [{colIndex: 0, selector: '#filter1'}, ...])
 */
function initializeMultipleColumnFilters(tableSelector, filters) {
    const table = $(tableSelector).DataTable();
    
    filters.forEach(filter => {
        $(filter.selector).on('change keyup', function() {
            table.column(filter.colIndex).search(this.value).draw();
        });
    });
}
