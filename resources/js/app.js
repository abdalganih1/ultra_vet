import './bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    // --- Logic for the data entry form ---
    const animalDataForm = document.getElementById('animalDataForm');
    if (!animalDataForm) {
        return; // Exit if not on the data entry page
    }

    // 1. Show/hide the 'custom animal type' field and handle EN translation
    const animalTypeSelect = document.getElementById('animal_type');
    const customAnimalTypeWrapper = document.getElementById('custom_animal_type_wrapper');
    const animalTypeEnInput = document.getElementById('animal_type_en');

    if (animalTypeSelect && customAnimalTypeWrapper && animalTypeEnInput) {
        const updateAnimalType = () => {
            const selectedOption = animalTypeSelect.options[animalTypeSelect.selectedIndex];
            
            // Show/hide 'Other' field
            if (animalTypeSelect.value === 'آخر') {
                customAnimalTypeWrapper.style.display = '';
            } else {
                customAnimalTypeWrapper.style.display = 'none';
            }

            // Set English translation in hidden input
            if (selectedOption && selectedOption.dataset.enName) {
                animalTypeEnInput.value = selectedOption.dataset.enName;
            } else {
                animalTypeEnInput.value = '';
            }
        };

        animalTypeSelect.addEventListener('change', updateAnimalType);
        // Trigger on load to set initial state
        updateAnimalType();
    }

    // 2. Add new vaccine entry
    const addVaccineBtn = document.getElementById('add-vaccine-entry');
    const vaccinesContainer = document.getElementById('vaccinations-container');
    if (addVaccineBtn && vaccinesContainer) {
        addVaccineBtn.addEventListener('click', function() {
            const newEntry = document.createElement('div');
            newEntry.classList.add('vaccine-entry', 'row', 'g-3', 'mb-3', 'align-items-end');
            // Note: The name attributes use `[]` to submit as an array.
            newEntry.innerHTML = `
                <div class="col-md-3">
                    <label class="form-label">نوع اللقاح:</label>
                    <select class="form-select vaccine-type" name="vaccine_type[]">
                        <option value="">اختر...</option>
                        <option value="سعار">سعار</option>
                        <option value="سباعي">سباعي</option>
                        <option value="ثلاثي">ثلاثي</option>
                        <option value="آخر">آخر</option>
                    </select>
                </div>
                <div class="col-md-3 other-vaccine-name-wrapper" style="display: none;">
                    <label class="form-label">اسم اللقاح الآخر:</label>
                    <input type="text" class="form-control" name="other_vaccine_name[]" value="">
                </div>
                <div class="col-md-2">
                    <label class="form-label">الشركة المصنعة:</label>
                    <input type="text" class="form-control" name="vaccine_manufacturer[]" value="">
                </div>
                <div class="col-md-2">
                    <label class="form-label">تاريخ الإعطاء:</label>
                    <input type="date" class="form-control" name="vaccine_date_given[]" value="">
                </div>
                <div class="col-md-2">
                    <label class="form-label">التاريخ التالي:</label>
                    <input type="date" class="form-control" name="vaccine_date_next[]" value="">
                </div>
                <div class="col-md-12 text-end">
                    <button type="button" class="btn btn-danger btn-sm remove-vaccine-entry">حذف</button>
                </div>
            `;
            vaccinesContainer.appendChild(newEntry);
        });
    }

    // 3. Handle dynamic events within the vaccinations container (remove and show/hide other)
    if (vaccinesContainer) {
        vaccinesContainer.addEventListener('click', function(e) {
            // Event for the remove button
            if (e.target && e.target.classList.contains('remove-vaccine-entry')) {
                e.target.closest('.vaccine-entry').remove();
            }

            // Event for the vaccine type dropdown
            if (e.target && e.target.classList.contains('vaccine-type')) {
                const wrapper = e.target.closest('.vaccine-entry').querySelector('.other-vaccine-name-wrapper');
                if (wrapper) {
                    if (e.target.value === 'آخر') {
                        wrapper.style.display = '';
                    } else {
                        wrapper.style.display = 'none';
                    }
                }
            }
        });
    }
});
