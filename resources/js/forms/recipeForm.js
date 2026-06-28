export default function recipeForm(
    ingredients,
    steps,
    tags,
    availableTags
) {

    return {
        ingredients,
        steps,
        tags,
        availableTags,
        loading: false,

        async generateTags() {
            this.loading = true;

            try {
                const response = await fetch('/recipes/generate-tags', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content
                    },

                    body: JSON.stringify({
                        title: document.querySelector('[name="title"]').value,
                        category_id: document.querySelector('[name="category_id"]').value,
                        ingredients: this.ingredients,
                        steps: this.steps
                            .map(step=>step.trim())
                            .filter(step=>step !== '')
                    })
                });

                if (!response.ok) {
                    //throw new Error('Unable to generate tags.');
                    console.log(await response.json());
                    return;
                }
                const data = await response.json();
                this.tags = data.tags;
            }

            catch (error) {
                console.error(error);
                alert('Unable to generate AI tags.');
            }

            finally {
                this.loading = false;
            }
        }
    }
}
