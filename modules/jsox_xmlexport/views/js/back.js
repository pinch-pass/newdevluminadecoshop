document.addEventListener('DOMContentLoaded', function () {
    JXC.init();
}, false);

const JXC = {
    config: null,
    setChooserTo: null,
    ajaxLink: window.location.href + '&setJsoxXmlConfig',
    resultLink: null,
    currentIndexPreset: null,

    init: function () {
        console.log('init JXC');

        this.bindVars();
        this.fillPresets();
        this.changeChooseConfigName();

        this.$updateConfig.off().click(function () {
            $(this).attr('disabled', 'disabled');
            JXC.getConfigFormData();
            JXC.setJsoxXmlConfig();
        });

        this.$chooseConfigName.off().on('change', function () {
            JXC.changeChooseConfigName();
            // JXC.init();
        })
    },

    setJsoxXmlConfig: function () {
        var obj = this.config;
        var json = JSON.stringify(obj);

        if ($('#jsoxConfigXmlForm #сonfig_name').val().length < 3) {
            alert('Введите имя пресета. Минимум 3 символа.');
            this.$updateConfig.removeAttr('disabled');
            return false;
        };
        $.ajax({
            type: 'POST',
            url: JXC.ajaxLink + '&rand=' + new Date().getTime(),
            data: {
                config: json
            },
            // dataType: 'JSON',
            success: function (answer) {
                JXC.$updateConfig.removeAttr('disabled').removeClass('btn-danger').addClass('btn-success');
                JXC.$updateConfig.html('Сохранено!');
                setTimeout(() => {
                    JXC.$updateConfig.html('Обновить').removeClass('btn-success').addClass('btn-danger');
                }, 2000);
                JXC.config = JSON.parse(answer);
                if (JXC.setChooserTo) {
                    JXC.fillPresets();
                    JXC.fillForm(JXC.setChooserTo);
                    JXC.$chooseConfigName.find('option').removeAttr('selected');
                    JXC.$chooseConfigName.find('option[value="' + JXC.setChooserTo + '"]').attr('selected', 'selected');
                    JXC.$сonfig_name.attr('disabled', 'disabled');
                }
            },
            error: function () {
                alert('ERROR UPDATE');
            }
        });
    },

    changeChooseConfigName: function () {
        var choosed = this.getChoosedXmlName();
        if (choosed == 'new') {
            this.setConfigNameNew();
        } else {
            this.setConfigName(choosed);
        }
    },

    setConfigNameNew: function () {
        this.$сonfig_name.val('').attr('type', 'text').removeAttr('disabled');
        this.$сonfig_name.off().bind('change keyup input click paste', function () {
            if (this.value.match(/[^0-9a-zA-Z_-]/g)) {
                this.value = this.value.replace(/[^0-9a-zA-Z\_\-]/g, '');
            }
        })
        this.$removeConfigBtn.fadeOut(500);
    },

    setConfigName: function (val) {
        this.$сonfig_name.val(val).attr('disabled', 'disabled');
        this.fillForm(val);
        this.$removeConfigBtn.fadeIn(500);
    },

    fillPresets: function () {
        this.$chooseConfigName.html('');
        this.$chooseConfigName.append(
            $('<option>', {
                value: 'new',
                text: 'Новый пресет XML',
            })
        )
        this.config.map(function (n, i) {
            if (n && n.length) {
                var name = n[n.length - 1]['сonfig_name'];
                JXC.$chooseConfigName.append(
                    $('<option>', {
                        value: name,
                        text: name,
                    })
                )
            }
        })
    },

    fillForm: function (presetName) {
        this.config.map(function (n, i) {
            if (n && n.length) {
                var name = n[n.length - 1]['сonfig_name'];
                if (name == presetName) {
                    JXC.currentIndexPreset = i;
                    JXC.fillFormBy(n, name);
                }
            }
        })
        if (JXC.currentIndexPreset !== null) {
            this.$removeConfigBtn.off().click(function () {
                if (confirm('Этот XML больше не будет доступен. Удалить?')) {
                    console.log(JXC.currentIndexPreset);
                    delete JXC.config[JXC.currentIndexPreset];
                    JXC.setJsoxXmlConfig();
                    JXC.clearForm();
                    JXC.init();
                }
            });
        }
    },

    clearForm: function () {
        var i = 0;
        var form = JXC.$jsoxConfigXmlForm;
        this.$resultLink.val('');
        form.find('input').each(function () {
            i = i + 30;
            setTimeout(() => {
                if ($(this).attr('type') == 'checkbox') {
                    $(this).removeAttr('checked');
                } else {
                    if ($(this).attr('name') != 'сonfig_name') {
                        $(this).val('');
                    }
                }
            }, i);
        })
        return i;
    },

    fillFormBy: function (n, nname) {
        n.map(function (v) {
            var form = JXC.$jsoxConfigXmlForm;
            var i = JXC.clearForm();
            setTimeout(() => {
                if (v['name']) {
                    var name = v['name'];
                    var value = v['value'];

                    form.find('input[name="' + name + '"]').each(function () {
                        var $t = $(this);
                        if ($t.attr('type') == 'checkbox') {
                            if ($t.val() == value) {
                                $t.attr('checked', 'checked')
                            }
                        } else {
                            $(this).val(value);
                        }
                    })
                }
                JXC.$resultLink.val(JXC.resultLink + nname)
            }, i + 50);

        })
    },

    clearName: function (str) {
        return str.replace(/[^0-9a-zA-Z\_\-]/g, '');
    },

    getChoosedXmlName: function () {
        return $('#chooseConfigName').val();
    },

    getConfigFormData: function () {
        var unindexed_array = this.$jsoxConfigXmlForm.serializeArray();
        var presetName = $('#jsoxConfigXmlForm #сonfig_name').val();
        var wn = { 'сonfig_name': presetName }
        unindexed_array.push(wn);

        var exists = false;
        $.map(this.config, function (n, i) {
            if (n && n.length) {
                var name = n[n.length - 1]['сonfig_name'];
                JXC.setChooserTo = presetName;

                if ((typeof JXC.config[i]) !== undefined) {
                    if (presetName == name) {
                        exists = true;
                        JXC.config[i] = unindexed_array;
                    }
                }
            }
        });
        if (!exists) {
            JXC.config.push(unindexed_array)
        }

        return this.config;
    },

    bindVars: function () {
        this.$jsoxConfigXmlForm = $('#jsoxConfigXmlForm');
        this.$chooseConfigName = $('#chooseConfigName');
        this.$сonfig_name = $('#jsoxConfigXmlForm #сonfig_name');
        this.$updateConfig = $('#updateConfig');
        this.$resultLink = $('#resultLink');
        this.$removeConfigBtn = $('#removeConfig');
    }
}
