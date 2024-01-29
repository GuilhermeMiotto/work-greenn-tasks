<template>
  <div class="signup-container">
    <div class="signup-content">
      <div class="signup-form">
        <h2>Cadastre-se</h2>
        <label class="input-label" for="fullname">
          <i class="fas fa-user icon"></i>
          <span class="label-text">Nome completo :</span>
        </label>
        <input
          class="input-field"
          type="text"
          v-model="fullname"
          id="fullname"
          placeholder="Seu Nome Completo"
        />
        <label class="input-label" for="email">
          <i class="fa-solid fa-envelope icon"></i>
          <span class="label-text">Endereço de email :</span>
        </label>
        <input
          class="input-field"
          type="email"
          v-model="email"
          id="email"
          placeholder="exemplo@gmail.com"
        />
        <label class="input-label" for="password">
          <i class="fa-solid fa-lock icon"></i>
          <span class="label-text">Senha de acesso :</span>
        </label>
        <input
          class="input-field"
          type="password"
          v-model="password"
          id="password"
          placeholder="*********************"
        />
        <router-link to="/homepage">
          <button class="login-button">Cadastrar</button>
        </router-link>
        <div class="signup-link">
          <p>
            Já tem uma conta?
            <span>
              <router-link to="/login"> Faça login agora mesmo! </router-link>
            </span>
          </p>
        </div>
      </div>

      <div class="black-square" @mouseover="changeColor" @mouseout="resetColor">
        <img
          v-if="isMouseOver"
          class="whitebox"
          src="@/assets/whitebox.png"
          alt="White Box"
        />
        <img
          v-else
          class="blackbox"
          src="@/assets/blackbox.png"
          alt="Black Box"
        />

        <img class="logo" src="@/assets/sing.svg" alt="Logo" />
        <div class="slogan" :style="{ color: textColor }">
          <div class="textblock">
            <p>
              Não fale dos seus sonhos para as pessoas,
              <span><br />Simplesmente mostre a elas!</span>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
  
  <script>
export default {
  data() {
    return {
      fullname: "",
      email: "",
      password: "",
      textColor: "black",
      isMouseOver: false,
    };
  },
  methods: {
    changeColor() {
      this.textColor = "white";
      this.isMouseOver = true;
    },
    resetColor() {
      this.textColor = "black";
      this.isMouseOver = false;
    },
  },
};
</script>
  
  <style scoped>
@import "~typeface-montserrat/index.css";

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Montserrat", sans-serif;
}

.signup-container {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
}

.signup-content {
  display: flex;
  align-items: center;
  border: 2px solid #8c858557;
  border-radius: 10px;
  padding: 40px;
}

.signup-form {
  margin-left: 40px;
  text-align: center;
  width: 535px;
  height: 375px;
  padding: 20px;
  margin-top: 28px;
  margin-right: 30px;
}

.black-square {
  width: 609px;
  height: 599px;
  background-color: rgba(245, 245, 245, 0.598);
  text-align: center;
  padding: 20px;
  position: relative;
  transition: background-color 0.3s;
}

.black-square:hover {
  background-color: #000000;
}

.blackbox,
.whitebox {
  width: 250px;
  height: 125px;
  position: absolute;
  top: 70%;
  right: 50%;
}

.logo {
  width: 80px;
  height: 80px;
  position: absolute;
  top: 50px;
  left: 95px;
}

.slogan {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 14px;
}

.signup-link {
  margin-top: 20px;
  font-size: 14px;
}

h2 {
  margin-bottom: 20px;
}

.textblock p {
  display: flex;
  font-size: 25px;
  white-space: nowrap;
}

.textblock span {
  font-weight: bold;
  display: flex;
  margin-top: 5px;
  position: absolute;
}

.input-label {
  display: flex;
  align-items: center;
  margin-top: 10px;
  font-size: 16px;
  font-weight: bold;
}

.label-text {
  margin-left: 10px;
}

.input-field {
  width: 100%;
  padding: 8px;
  margin-top: 5px;
  margin-bottom: 15px;
  box-sizing: border-box;
}

.login-button {
  background-color: #000000;
  color: #ffffff;
  padding: 15px 40px;
  border: none;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s, color 0.3s;
}

.login-button:hover {
  background-color: #a19d9d;
  color: #000000;
}

.icon {
  font-family: "Font Awesome 5 Free";
  color: #000000;
  margin-right: 0px;
}
</style>
  