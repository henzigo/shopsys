export const en = {
    assetManager: {
        addButton: 'Insert image',
        inputPlh: 'https://path/to/the/image.jpg',
        modalTitle: 'Choose image',
        uploadTitle: 'Drag images here or click upload'
    },
    // Here just as a reference, GrapesJS core doesn't contain any block,
    // so this should be omitted from other local files
    blockManager: {
        labels: {
            // 'block-id': 'Block Label',
        },
        categories: {
            // 'category-id': 'Category Label',
        }
    },
    domComponents: {
        names: {
            '': 'Box',
            wrapper: 'Wrapper',
            text: 'Text',
            comment: 'Comment',
            image: 'Image',
            video: 'Video',
            label: 'Label',
            link: 'Link',
            map: 'Map',
            tfoot: 'Table footer',
            tbody: 'Table body',
            thead: 'Table header',
            table: 'Table',
            row: 'Table row',
            cell: 'Table cell'
        }
    },
    deviceManager: {
        device: 'Device',
        devices: {
            desktop: 'Desktop',
            tablet: 'Tablet',
            mobileLandscape: 'Mobil landscape',
            mobilePortrait: 'Mobil portrait'
        }
    },
    panels: {
        buttons: {
            titles: {
                preview: 'Preview',
                fullscreen: 'Fullscreen',
                'sw-visibility': 'Components',
                'export-template': 'Show code',
                'open-sm': 'Oped style manager',
                'open-tm': 'Settings',
                'open-layers': 'Open layer editor',
                'open-blocks': 'Open blocks',
                undo: 'Undo',
                redo: 'Redo',
                save: 'Save',
                close: 'Cloee',
                cmdClear: 'Clear changes',
                cmdTglImages: 'Toggle images',
                cmdOpenImport: 'Import content',
                activateOutline: 'Components'
            }
        },
        options: {
            devices: {
                cmdDeviceDesktop: 'Desktop',
                cmdDeviceTablet: 'Tablet',
                cmdDeviceMobile: 'Mobil'
            }
        }
    },
    selectorManager: {
        label: 'Classes',
        selected: 'Selected',
        emptyState: '- State -',
        states: {
            hover: 'Hover',
            active: 'Click',
            'nth-of-type(2n)': 'Even/odd'
        }
    },
    styleManager: {
        empty: 'Select an element before using the Style Editor',
        layer: 'Layer',
        fileButton: 'Images',
        sectors: {
            general: 'General',
            layout: 'Layout',
            typography: 'Typography',
            decorations: 'Decorations',
            extra: 'Extra',
            flex: 'Flex',
            dimension: 'Dimension'
        },
        // Default names for sub properties in Composite and Stack types.
        // Other labels are generated directly from their property names (eg. 'font-size' will be 'Font size').
        properties: {
            'text-shadow-h': 'X',
            'text-shadow-v': 'Y',
            'text-shadow-blur': 'Blur',
            'text-shadow-color': 'Color',
            'box-shadow-h': 'X',
            'box-shadow-v': 'Y',
            'box-shadow-blur': 'Blur',
            'box-shadow-spread': 'Spread',
            'box-shadow-color': 'Color',
            'box-shadow-type': 'Type',
            'margin-top-sub': 'Top',
            'margin-right-sub': 'Right',
            'margin-bottom-sub': 'Bottom',
            'margin-left-sub': 'Left',
            'padding-top-sub': 'Top',
            'padding-right-sub': 'Right',
            'padding-bottom-sub': 'Bottom',
            'padding-left-sub': 'Left',
            'border-width-sub': 'Width',
            'border-style-sub': 'Style',
            'border-color-sub': 'Color',
            'border-top-left-radius-sub': 'Top left',
            'border-top-right-radius-sub': 'Top right',
            'border-bottom-right-radius-sub': 'Bottom right',
            'border-bottom-left-radius-sub': 'Bottom left',
            'transform-rotate-x': 'Rotate X',
            'transform-rotate-y': 'Rotate Y',
            'transform-rotate-z': 'Rotate Z',
            'transform-scale-x': 'Scale X',
            'transform-scale-y': 'Scale Y',
            'transform-scale-z': 'Scale Z',
            'transition-property-sub': 'Property',
            'transition-duration-sub': 'Duration',
            'transition-timing-function-sub': 'Timing',
            'background-image-sub': 'Image',
            'background-repeat-sub': 'Repeat',
            'background-position-sub': 'Position',
            'background-attachment-sub': 'Attachment',
            'background-size-sub': 'Size'
        }
        // Translate options in style properties
        // options: {
        //   float: { // Id of the property
        //     ...
        //     left: 'Left', // {option id}: {Option label}
        //   }
        // }
    },
    traitManager: {
        empty: 'Select an element before using the property editor',
        label: 'Component settings',
        traits: {
            // The core library generates the name by their `name` property
            labels: {
                id: 'Id',
                alt: 'Alt',
                title: 'Title',
                href: 'Href',
                target: 'Target',
                provider: 'Provider',
                src: 'Url',
                poster: 'Poster',
                autoplay: 'Autoplay',
                loop: 'Loop',
                controls: 'Controls',
                startfrom: 'Start from',
                endText: 'End text'
            },
            // In a simple trait, like text input, these are used on input attributes
            /*
            attributes: {
                id: traitInputAttr,
                alt: traitInputAttr,
                title: traitInputAttr,
                href: { placeholder: 'eg. https://google.com' }
            },
            */
            // In a trait like select, these are used to translate option names
            options: {
                target: {
                    false: 'This window',
                    _blank: '_blank'
                }
            }
        }
    },
    storageManager: {
        recover: 'Do you want to restore unsaved changes?'
    }
};
