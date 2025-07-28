(function () {
    const namespace = BX.namespace('ex31.menu');

    namespace.showFacts = async function () {
        try {
            const response = await BX.ajax.runAction('b24:academy.api.CompanyFact.getFact', {method: 'GET'});

            const popup = BX.PopupWindowManager.create('company-fact', null, {
                content: response.data.fact,
                autoHide: false,
                width: 450,
                height: 700,
                closeIcon: {
                    opacity: 1,
                },
                titleBar: BX.message('COMPANY_FACT')
            });
            popup.show();
        } catch (e) {
            console.error(e);
        }
    }
})();