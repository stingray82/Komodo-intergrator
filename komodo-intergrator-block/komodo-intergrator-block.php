<?php
/**
 * Plugin Name: Komodo Decks Integrator
 * Description: A custom block to integrate Komodo Decks iframe.
 * Version: 1.0
 * Author: STINGRAY82
 */
function komodo_decks_block_init() {
    ?>
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        (function(wp) {
            if (!wp || !wp.element || !wp.blocks) {
                return;
            }

            // Unique variable names for Komodo block
            var elKomodo = wp.element.createElement;
            var InspectorControlsKomodo = wp.blockEditor.InspectorControls;
            var TextControlKomodo = wp.components.TextControl;
            var PanelBodyKomodo = wp.components.PanelBody;
            var ToggleControlKomodo = wp.components.ToggleControl;
            var SelectControlKomodo = wp.components.SelectControl;
            var RangeControlKomodo = wp.components.RangeControl;
            var useBlockPropsKomodo = wp.blockEditor.useBlockProps;

            // Define a unique SVG icon for the Komodo Decks block
            var blockIconKomodo = elKomodo('svg', { 
                xmlns: "http://www.w3.org/2000/svg", 
                viewBox: "0 0 72 72", 
                width: "24", 
                height: "24"
            },
                elKomodo('g', { fill: "none" },
                    elKomodo('rect', { width: "72", height: "72", fill: "#75D670" }), // Background
                    elKomodo('path', { 
                        d: "M11 9h14l1 23 4-5.313 1.353-1.761c2.034-2.665 3.856-5.28 5.569-8.172C38.769 13.718 40.316 11.337 43 9c4.48-.903 8.5-.68 13 0 0 4.863-1.771 6.785-4.688 10.375l-1.318 1.672c-3.552 4.484-3.552 4.484-5.477 6.528-2.811 3.004-2.811 3.004-3.599 6.913 1.41 3.271 3.241 5.863 5.395 8.7l2.363 3.167c3.25 3.7 4.905 4.475 9.777 5.06l3.797.21 3.828.227L69 52c-.71 1.922-.71 1.922-2 4-2.164.828-2.164.828-4.625 1.25l-2.477.453L58 58v2l12 3-1 2c-18.87 1.1-18.87 1.1-25.047-4.02C38.621 55.574 33.718 49.948 29 44c-3.553 3.62-3.539 7.438-3.688 12.25l-.103 2.258c-.082 1.83-.147 3.661-.209 5.492H11V9Z",
                        fill: "#FAFDFA" // Adjust the fill color as needed
                    })
                )
            );

            wp.blocks.registerBlockType('custom/komodo-decks-integrator', {
                title: 'Komodo Decks Integrator',
                icon: blockIconKomodo, // Use the unique icon variable here
                category: 'embed',
                attributes: {
                    embedID: {
                        type: 'string',
                        default: '',
                    },
                    onlyRecording: {
                        type: 'boolean',
                        default: true,
                    },
                    sizePreset: {
                        type: 'string',
                        default: 'medium',
                    },
                    width: {
                        type: 'number',
                        default: 100,
                    },
                    height: {
                        type: 'number',
                        default: 56.25,
                    },
                    margin: {
                        type: 'number',
                        default: 0,
                    },
                    border: {
                        type: 'string',
                        default: '0',
                    },
                    allowFullscreen: {
                        type: 'boolean',
                        default: true,
                    },
                },
                edit: function(props) {
                    var embedID = props.attributes.embedID;
                    var onlyRecording = props.attributes.onlyRecording;
                    var sizePreset = props.attributes.sizePreset;

                    function onChangeEmbedID(newID) {
                        if (newID.startsWith('http')) {
                            var extractedID = newID.split('/')[4];  
                            props.setAttributes({ embedID: extractedID });
                        } else {
                            props.setAttributes({ embedID: newID });
                        }
                    }

                    function getSizeStyles(preset, width, height) {
                        switch (preset) {
                            case 'small':
                                return { width: '40%', paddingBottom: '30%' };
                            case 'medium':
                                return { width: '60%', paddingBottom: '33.75%' };
                            case 'large':
                                return { width: '80%', paddingBottom: '38.1%' };
                            case 'manual':
                                return { width: width + '%', paddingBottom: height + '%' };
                            default:
                                return { width: '60%', paddingBottom: '33.75%' };
                        }
                    }

                    var sizeStyles = getSizeStyles(sizePreset, props.attributes.width, props.attributes.height);

                    var iframeURL = embedID 
                        ? `https://komododecks.com/embed/recordings/${embedID}?onlyRecording=${onlyRecording ? '1' : '0'}`
                        : '';
                    
                    var blockProps = useBlockPropsKomodo({
                        className: 'komodo-decks-integrator-block',
                        style: {
                            margin: props.attributes.margin + 'px auto',
                            border: props.attributes.border + 'px solid black',
                            position: 'relative',
                            width: sizeStyles.width,
                            height: 0,
                            paddingBottom: sizeStyles.paddingBottom,
                            overflow: 'hidden',
                        }
                    });

                    return elKomodo('div', blockProps,
                        elKomodo(InspectorControlsKomodo, {},
                            elKomodo(PanelBodyKomodo, { title: 'Komodo Decks Integrator Settings', initialOpen: true },
                                elKomodo(TextControlKomodo, {
                                    label: 'Recording ID or URL',
                                    value: embedID,
                                    onChange: onChangeEmbedID,
                                    placeholder: 'Enter the unique ID or full URL here',
                                }),
                                elKomodo(ToggleControlKomodo, {
                                    label: 'Only Recording',
                                    checked: onlyRecording,
                                    onChange: function(newValue) {
                                        props.setAttributes({ onlyRecording: newValue });
                                    }
                                }),
                                elKomodo(SelectControlKomodo, {
                                    label: 'Size Preset',
                                    value: sizePreset,
                                    options: [
                                        { label: 'Small', value: 'small' },
                                        { label: 'Medium', value: 'medium' },
                                        { label: 'Large', value: 'large' },
                                        { label: 'Manual', value: 'manual' }
                                    ],
                                    onChange: function(newPreset) {
                                        props.setAttributes({ sizePreset: newPreset });
                                    }
                                }),
                                sizePreset === 'manual' && elKomodo(RangeControlKomodo, {
                                    label: 'Width (%)',
                                    value: props.attributes.width,
                                    onChange: function(newWidth) {
                                        props.setAttributes({ width: newWidth });
                                    },
                                    min: 10,
                                    max: 100,
                                }),
                                sizePreset === 'manual' && elKomodo(RangeControlKomodo, {
                                    label: 'Height (%)',
                                    value: props.attributes.height,
                                    onChange: function(newHeight) {
                                        props.setAttributes({ height: newHeight });
                                    },
                                    min: 10,
                                    max: 100,
                                }),
                                elKomodo(RangeControlKomodo, {
                                    label: 'Margin (px)',
                                    value: props.attributes.margin,
                                    onChange: function(newMargin) {
                                        props.setAttributes({ margin: newMargin });
                                    },
                                    min: 0,
                                    max: 100,
                                }),
                                elKomodo(TextControlKomodo, {
                                    label: 'Border (px)',
                                    value: props.attributes.border,
                                    onChange: function(newBorder) {
                                        props.setAttributes({ border: newBorder });
                                    },
                                    placeholder: 'Enter border width (e.g., 2)',
                                }),
                                elKomodo(ToggleControlKomodo, {
                                    label: 'Allow Fullscreen',
                                    checked: props.attributes.allowFullscreen,
                                    onChange: function(newValue) {
                                        props.setAttributes({ allowFullscreen: newValue });
                                    }
                                })
                            )
                        ),
                        embedID 
                        ? elKomodo('iframe', {
                                src: iframeURL,
                                style: {
                                    border: 0,
                                    position: 'absolute',
                                    inset: 0,
                                    width: '100%',
                                    height: '100%',
                                },
                                allowFullscreen: props.attributes.allowFullscreen ? 'allowfullscreen' : '',
                            })
                        : elKomodo('p', {}, 'Please enter a valid Recording ID or URL in the settings panel.')
                    );
                },
                save: function(props) {
                    var embedID = props.attributes.embedID;
                    var onlyRecording = props.attributes.onlyRecording;
                    var sizePreset = props.attributes.sizePreset;
                    var sizeStyles = getSizeStyles(sizePreset, props.attributes.width, props.attributes.height);

                    var iframeURL = embedID 
                        ? `https://komododecks.com/embed/recordings/${embedID}?onlyRecording=${onlyRecording ? '1' : '0'}`
                        : '';
                    var style = {
                        margin: props.attributes.margin + 'px auto',
                        border: props.attributes.border + 'px solid black',
                        position: 'relative',
                        width: sizeStyles.width,
                        height: 0,
                        paddingBottom: sizeStyles.paddingBottom,
                        overflow: 'hidden',
                    };

                    return embedID 
                        ? elKomodo('div', { className: 'komodo-decks-integrator-block', style: style },
                            elKomodo('iframe', {
                                src: iframeURL,
                                style: {
                                    border: 0,
                                    position: 'absolute',
                                    inset: 0,
                                    width: '100%',
                                    height: '100%',
                                },
                                allowFullscreen: props.attributes.allowFullscreen ? 'allowfullscreen' : '',
                            })
                          )
                        : null;
                }
            });

            function getSizeStyles(preset, width, height) {
                switch (preset) {
                    case 'small':
                        return { width: '40%', paddingBottom: '30%' };
                    case 'medium':
                        return { width: '60%', paddingBottom: '33.75%' };
                    case 'large':
                        return { width: '80%', paddingBottom: '38.1%' };
                    case 'manual':
                        return { width: width + '%', paddingBottom: height + '%' };
                    default:
                        return { width: '60%', paddingBottom: '33.75%' };
                }
            }
        })(window.wp);
    });
    </script>
    <?php
}

add_action('admin_footer', 'komodo_decks_block_init');

function komodo_decks_enqueue_block_styles() {
    wp_enqueue_style(
        'komodo-decks-block-styles',
        plugins_url('css/komodo-decks.css', __FILE__), 
        array(), 
        '1.0.0' 
    );
}

add_action('wp_enqueue_scripts', 'komodo_decks_enqueue_block_styles');
