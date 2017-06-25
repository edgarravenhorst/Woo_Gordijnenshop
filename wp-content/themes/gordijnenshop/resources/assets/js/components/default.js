export default class DefaultComponent {

  constructor(){
    // Objects
    this.$el = document.querySelector('.scroll-down'); // Primairy Element

    // Check object exsists
    if(!this.$el) return;

    // Variables
    this.variable = 2;

    // Event listeners
    this.$el.addEventListener('click',this.functionname.bind(this));
  }

  // Other functions
  functionname(){
  }

  functionname2(){
  }
}

// == Use in site.js as following: ==
// import DefaultComponent from './components/DefaultComponent.js';

// jQuery( document ).ready(function() {
//   var componentdefault = new DefaultComponent();
// });
