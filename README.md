# Qui Paie Quoi / Who Pays What ?
*This is a training project made during my apprenticeship at KNP Labs*

This is a Symfony project running with Docker compose. 

The purpose of this web app is to share expenses and bills with your friends/associates.

- Create and Join groups
- Add what you paid
- Get the balance of what you owe to who and what others owe you

## Requirements

- [Docker engine `> 20.10`](https://docs.docker.com/engine/install/)
- [Docker compose `> 2`](https://docs.docker.com/compose/install/linux/)

## Get started

1. **Clone** the project
    ```bash
    git clone git@github.com:KnpLabs/qui-paie-quoi-gael.git
    ```
2. And **launch the project** for the first time : 
    ```bash
    make copy-env start migrations fixtures
    ```

At this point you should have a **working symfony project** accessible on [localhost](http://localhost).

You can use the fixture user account **Alice**: 
- email: `alice@test.com`
- password: `password` 

