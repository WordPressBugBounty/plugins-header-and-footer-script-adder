/**
 * Admin JavaScript for Advanced Scripts Manager
 *
 * @package    AdvancedScriptsManager
 * @subpackage AdvancedScriptsManager/admin/js
 * @since      2.0.3
 */

(function($) {
    'use strict';

    /**
     * Initialize the plugin when document is ready
     */
    $(document).ready(function() {
        ASM_Admin.init();
    });

    /**
     * Main admin object
     */
    var ASM_Admin = {
        
        /**
         * Initialize all admin functionality
         */
        init: function() {
            this.initCodeEditors();
            this.initFormValidation();
            this.initTooltips();
            this.initConfirmations();
            this.bindEvents();
        },

        /**
         * Initialize code editors using WordPress CodeMirror
         */
        initCodeEditors: function() {
            // Check if wp.codeEditor is available
            if (typeof wp !== 'undefined' && wp.codeEditor) {
                // Initialize code editors for all textareas with asm-code-editor class
                $('.asm-code-editor').each(function() {
                    var $textarea = $(this);
                    var editorId = $textarea.attr('id');
                    
                    // Skip if already initialized
                    if ($textarea.data('asm-editor-initialized')) {
                        return;
                    }
                    
                    // CodeMirror settings
                    var editorSettings = {
                        codemirror: {
                            mode: 'htmlmixed',
                            lineNumbers: true,
                            lineWrapping: true,
                            indentUnit: 2,
                            tabSize: 2,
                            indentWithTabs: false,
                            autoCloseTags: true,
                            autoCloseBrackets: true,
                            matchBrackets: true,
                            styleActiveLine: true,
                            theme: 'default',
                            extraKeys: {
                                'Ctrl-Space': 'autocomplete',
                                'F11': function(cm) {
                                    cm.setOption('fullScreen', !cm.getOption('fullScreen'));
                                },
                                'Esc': function(cm) {
                                    if (cm.getOption('fullScreen')) cm.setOption('fullScreen', false);
                                }
                            }
                        }
                    };
                    
                    // Initialize the editor
                    var editor = wp.codeEditor.initialize(editorId, editorSettings);
                    
                    if (editor) {
                        // Mark as initialized
                        $textarea.data('asm-editor-initialized', true);
                        
                        // Store editor instance
                        $textarea.data('asm-editor-instance', editor);
                        
                        // Auto-resize editor
                        editor.codemirror.on('change', function(cm) {
                            ASM_Admin.autoResizeEditor(cm);
                        });
                        
                        // Initial resize
                        setTimeout(function() {
                            ASM_Admin.autoResizeEditor(editor.codemirror);
                        }, 100);
                    }
                });
            }
        },

        /**
         * Auto-resize CodeMirror editor based on content
         */
        autoResizeEditor: function(cm) {
            var lineCount = cm.lineCount();
            var minLines = 10;
            var maxLines = 50;
            var lines = Math.max(minLines, Math.min(maxLines, lineCount + 2));
            var height = lines * 20; // Approximate line height
            
            cm.setSize(null, height + 'px');
        },

        /**
         * Initialize form validation
         */
        initFormValidation: function() {
            // Validate settings form before submission
            $('form').on('submit', function(e) {
                var $form = $(this);
                var isValid = true;
                var errors = [];

                // Check for potentially dangerous scripts
                $form.find('.asm-code-editor').each(function() {
                    var $textarea = $(this);
                    var content = $textarea.val().toLowerCase();
                    var fieldName = $textarea.closest('.asm-field-group').find('label').text() || 'Script field';

                    // Basic security checks
                    if (content.includes('eval(') || content.includes('document.write(')) {
                        errors.push(fieldName + ': Consider avoiding eval() and document.write() for security reasons.');
                    }

                    // Check for unclosed tags
                    var openTags = (content.match(/<script[^>]*>/g) || []).length;
                    var closeTags = (content.match(/<\/script>/g) || []).length;
                    if (openTags !== closeTags) {
                        errors.push(fieldName + ': Unclosed <script> tags detected.');
                        isValid = false;
                    }
                });

                // Show warnings (but don't prevent submission)
                if (errors.length > 0) {
                    var message = 'Potential issues detected:\n\n' + errors.join('\n\n') + '\n\nDo you want to continue?';
                    if (!confirm(message)) {
                        e.preventDefault();
                        return false;
                    }
                }

                // Prevent submission if critical errors
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fix the errors before saving.');
                    return false;
                }
            });
        },

        /**
         * Initialize tooltips for help text
         */
        initTooltips: function() {
            // Add help icons with tooltips
            $('.description').each(function() {
                var $desc = $(this);
                if ($desc.text().length > 50) {
                    $desc.addClass('asm-tooltip');
                }
            });
        },

        /**
         * Initialize confirmation dialogs
         */
        initConfirmations: function() {
            // Confirm before clearing large amounts of code
            $('.asm-code-editor').on('keydown', function(e) {
                // Ctrl+A or Cmd+A followed by Delete/Backspace
                if ((e.ctrlKey || e.metaKey) && e.keyCode === 65) {
                    var $textarea = $(this);
                    setTimeout(function() {
                        $textarea.data('asm-select-all', true);
                    }, 10);
                }
                
                if ($textarea.data('asm-select-all') && (e.keyCode === 8 || e.keyCode === 46)) {
                    if ($textarea.val().length > 100) {
                        if (!confirm('Are you sure you want to clear all the code in this field?')) {
                            e.preventDefault();
                            return false;
                        }
                    }
                    $textarea.removeData('asm-select-all');
                }
            });
        },

        /**
         * Bind various events
         */
        bindEvents: function() {
            // Toggle advanced options
            $('.asm-toggle-advanced').on('click', function(e) {
                e.preventDefault();
                var $toggle = $(this);
                var $target = $($toggle.data('target'));
                
                $target.slideToggle();
                $toggle.text($target.is(':visible') ? 'Hide Advanced Options' : 'Show Advanced Options');
            });

            // Copy to clipboard functionality
            $('.asm-copy-button').on('click', function(e) {
                e.preventDefault();
                var $button = $(this);
                var targetId = $button.data('target');
                var $target = $('#' + targetId);
                
                if ($target.length) {
                    $target.select();
                    document.execCommand('copy');
                    
                    // Show feedback
                    var originalText = $button.text();
                    $button.text('Copied!').addClass('asm-copied');
                    
                    setTimeout(function() {
                        $button.text(originalText).removeClass('asm-copied');
                    }, 2000);
                }
            });

            // Auto-save functionality (optional)
            if (typeof Storage !== 'undefined') {
                $('.asm-code-editor').on('input', function() {
                    var $textarea = $(this);
                    var fieldId = $textarea.attr('id');
                    var content = $textarea.val();
                    
                    // Save to localStorage as backup
                    localStorage.setItem('asm_backup_' + fieldId, content);
                });

                // Restore from backup if needed
                $('.asm-code-editor').each(function() {
                    var $textarea = $(this);
                    var fieldId = $textarea.attr('id');
                    var backup = localStorage.getItem('asm_backup_' + fieldId);
                    
                    if (backup && !$textarea.val() && backup.length > 10) {
                        if (confirm('A backup of this field was found. Would you like to restore it?')) {
                            $textarea.val(backup);
                        }
                    }
                });
            }

            // Clear backup on successful save
            $(document).on('submit', 'form', function() {
                if (typeof Storage !== 'undefined') {
                    $('.asm-code-editor').each(function() {
                        var fieldId = $(this).attr('id');
                        localStorage.removeItem('asm_backup_' + fieldId);
                    });
                }
            });
        },

        /**
         * Utility function to show notices
         */
        showNotice: function(message, type) {
            type = type || 'info';
            var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            
            $('.wrap h1').after($notice);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $notice.fadeOut();
            }, 5000);
        },

        /**
         * Utility function to validate script syntax
         */
        validateScript: function(content) {
            var errors = [];
            
            // Check for common syntax errors
            try {
                // Basic HTML validation
                var parser = new DOMParser();
                var doc = parser.parseFromString('<div>' + content + '</div>', 'text/html');
                var parseErrors = doc.getElementsByTagName('parsererror');
                
                if (parseErrors.length > 0) {
                    errors.push('HTML parsing errors detected');
                }
            } catch (e) {
                errors.push('Syntax validation failed: ' + e.message);
            }
            
            return errors;
        }
    };

    // Make ASM_Admin globally available
    window.ASM_Admin = ASM_Admin;

})(jQuery);

/**
 * Additional utility functions that don't require jQuery
 */

/**
 * Debounce function to limit function calls
 */
function asmDebounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

/**
 * Simple function to escape HTML
 */
function asmEscapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

/**
 * Toggle specific post/page selection visibility
 */
function toggleSpecificSelection(selectElement, location) {
    var specificDiv = document.getElementById(location + '_specific_selection');
    if (specificDiv) {
        if (selectElement.value === 'specific') {
            specificDiv.style.display = 'block';
        } else {
            specificDiv.style.display = 'none';
        }
    }
}