describe('DESCRIBE', () => {


  it('make request', () => {
    cy.visit('http://localhost/LEAF_Request_Portal/');
    cy.visit('http://localhost/LEAF_Request_Portal/');
    cy.get('.menu2 > a:nth-child(1) .menuTextSmall').click();
    cy.get('#title').click();
    cy.get('#title').type('test test test ');
    cy.get('.item:nth-child(3)').click();
    cy.get('.hover').click();
    cy.get('.item:nth-child(3)').click();
    cy.get('#formNextBtn').click();
    cy.get('#record').submit();
    cy.get('#nextQuestion2').click();
    cy.get('#2').click();
    cy.get('#2').type('firstname');
    cy.get('#nextQuestion2').click();
    cy.get('#3').click();
    cy.get('#3').type('last nanme');
    cy.get('#nextQuestion2').click();
    cy.get('#4').click();
    cy.get('#4').type('occupation');
    cy.get('#nextQuestion2 > img').click();
    cy.get('#5').click();
    cy.get('#5').type('hobbies');
    cy.get('#nextQuestion2').click();
    cy.get('#6').click();
    cy.get('.ui-state-highlight').click();
    cy.get('#nextQuestion2').click();
    cy.get('#7').click();
    cy.get('#7').type('masked');
    cy.get('.section').click();
    cy.get('#nextQuestion2 > img').click(); 

  }


}


