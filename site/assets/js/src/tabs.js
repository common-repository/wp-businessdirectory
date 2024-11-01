/**
 * JBD Tabs javascript class
 */
 class JBDTabs{

    /**
     * Constructor
     *
     */
	constructor() {
        this.currentTab = 1;
        this.currentTabIndex = 0;
        this.maxTabs = 6;
        this.tabMapInitialized = 0;
    }

    /**
     * Set maximum number of tabs on the page
     *
     * @param maxTabs int number of tabs in the page
     */
    setMaxTabs(maxTabs) {
        this.maxTabs = maxTabs;
    }

    /**
     * Set the number of the preset tabs on the page
     *
     * @param presentTabs int number of tabs
     */
    setPresentTabs(presentTabs) {
        this.presentTabs = presentTabs;
        this.setMaxTabs(presentTabs.length);
    }

    /**
     * Validate the current form and open the tab if everything is validated and is OK
     *
     * @param tab int ID of the tab
     */
    openTab(tab) {

        if (jbdUtils.getProperty("isMultilingual")) {
            let tabgroup = jQuery(".tab-" + jbdUtils.getProperty("defaultLang"));
            if (tabgroup.length == 0){
                tabgroup = jQuery("button[aria-controls='tab-" + jbdUtils.getProperty("defaultLang") + "']");
            }
            tabgroup.each(function () {
                jQuery(this).click();
            });
        }

        jQuery("#item-form").validationEngine('detach');
        if (jbdUtils.getProperty("validateRichTextEditors")) {
            jbdUtils.validateRichTextEditors();
        }

        jbdUtils.validateMultiSelects();
        let validationResult = jQuery("#item-form").validationEngine('validate');

        if (!validationResult) {
            return;
        }

        // console.debug(jQuery("#tab" + tab).hasClass("active"));
        // console.debug(tab);
        // console.debug("#tab" + tab);
        if(jQuery("#tab" + tab).hasClass("active")){
            jQuery("#tab" + tab).removeClass("active");
            jQuery("#edit-tab" + tab).hide();
        }else{
            this.showEditTab(tab);
        }
    }

    /**
     * Open the called tab and hide all the other
     *
     * @param tab int tab name
     */
    showEditTab(tab) {
        jQuery(".edit-tab").each(function () {
            jQuery(this).hide();
        });

        jQuery(".process-step").each(function () {
            jQuery(this).hide();
            jQuery(this).removeClass("active");

        });

        jQuery(".process-tab").each(function () {
            jQuery(this).removeClass("active");
            jQuery(this).removeClass("completed");
            jQuery(this).removeClass("not-completed");
        });

        if(jQuery("#next-btn").length || jQuery("#save-btn").length){
            //add completed class to completed tabs
            for(var i = 1; i<this.currentTabIndex+1;i++){
                jQuery("#tab" + i).addClass("completed");
            }

            //set not-completed for remaining tabs
            for(var i = this.currentTabIndex + 2; i<=this.maxTabs; i++){
                jQuery("#tab" + i).addClass("not-completed");
            }
        }

        if (this.currentTabIndex == 0) {
            jQuery("#prev-btn").hide();
        }
        else {
            jQuery("#prev-btn").show();
        }

        //console.debug(this.currentTabIndex);

        if ((this.currentTabIndex + 1) == this.maxTabs) {
            jQuery("#next-btn").hide();
            jQuery("#save-btn").show();
            jQuery(".jbd-terms-conditions").show();
            jQuery("#privacy_policy").show();
        }
        else {
            jQuery("#next-btn").show();
            jQuery("#save-btn").hide();
            jQuery(".jbd-terms-conditions").hide();
            jQuery("#privacy_policy").hide();
        }

        jQuery("#edit-tab" + tab).show();
        jQuery("#step" + tab).show();

        if(tab!=1){
            let scrollTopOffset = jQuery("#tab" + tab).offset().top - 150;
            jQuery('html,body').animate({scrollTop: scrollTopOffset}, 'slow');
        }

        jQuery("#step" + tab).addClass("active");
        jQuery("#tab" + tab).addClass("active");
        jQuery("#active-step-number").html(tab);
        if (tab == 3 && this.tabMapInitialized == 0) {
            //TODO global reference
            initializeMap();
            this.tabMapInitialized = 1;
        }
    }

    /**
     * Used on front end when creating a new listing and select next.
     * This function open the next tab
     */
    nextTab() {
        if (jbdUtils.getProperty("isMultilingual")) {
            let tabgroup = jQuery(".tab-" + jbdUtils.getProperty("defaultLang"));
            if (tabgroup.length == 0){
                tabgroup = jQuery("button[aria-controls='tab-" + jbdUtils.getProperty("defaultLang") + "']");
            }
            tabgroup.each(function () {
                jQuery(this).click();
            });
        }

        if (jbdUtils.getProperty("validateRichTextEditors")) {
            jbdUtils.validateRichTextEditors();
        }

        let validationResult = jQuery("#item-form").validationEngine('validate');
        if (validationResult) {
            if (this.currentTabIndex < this.presentTabs.length - 1) {
                this.currentTabIndex++;
                this.currentTab = this.presentTabs[this.currentTabIndex];
            }
            this.showEditTab(this.currentTab);
        }
    }

    /**
     * Used on front end when editing a listing and select previous.
     * This function open the previous tab
     */
    previousTab() {
        if (this.currentTabIndex > 0) {
            this.currentTabIndex--;
            this.currentTab = this.presentTabs[this.currentTabIndex];
        }

        this.showEditTab(this.currentTab);
    }
}

let jbdTabs = new JBDTabs();