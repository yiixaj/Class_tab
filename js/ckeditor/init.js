// Función para inicializar CKEditor en cualquier página
function initializeCKEditor(editorId = 'editor', toolbarId = 'toolbar-container') {
    const editor = document.querySelector(`#${editorId}`);
    if (!editor) return;

    DecoupledEditor
        .create(editor, {
            toolbar: {
                items: [
                    'undo', 'redo',
                    '|',
                    'heading',
                    '|',
                    'bold', 'italic', 'strikethrough', 'underline',
                    '|',
                    'fontSize', 'fontColor',
                    '|',
                    'alignment',
                    '|',
                    'bulletedList', 'numberedList',
                    '|',
                    'link', 'blockQuote', 'insertTable'
                ]
            },
            // Configuración adicional para manejar el contenido
            placeholder: 'Type your content here...',
            removePlugins: ['Title'],
            // Asegurar que el contenido se guarde correctamente en el formulario
            onChange: (eventInfo, editor) => {
                const data = editor.getData();
                document.querySelector(`#${editorId}`).value = data;
            }
        })
        .then(editor => {
            // Colocar la barra de herramientas en su contenedor
            const toolbarContainer = document.querySelector(`#${toolbarId}`);
            if (toolbarContainer) {
                toolbarContainer.appendChild(editor.ui.view.toolbar.element);
            }

            // Manejar el envío del formulario
            const form = editor.sourceElement.closest('form');
            if (form) {
                form.addEventListener('submit', () => {
                    const data = editor.getData();
                    editor.sourceElement.value = data;
                });
            }
        })
        .catch(error => {
            console.error('CKEditor initialization error:', error);
        });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    initializeCKEditor();
});